<?php


include("test_creds.php");

$conn = new mysqli($servername, $username, $password,$dbname);


$uploadDir = 'archives/';

// le type de document est classifié entre 0 et n dans l'ensemble des entiers naturels
$max_val_type = 3;

// Liste des extensions autorisées pour les images
$image_extensions = [
'jpg', 
'jpeg',
'png',
'gif',
'bmp',
'tiff', 
'tif',
'webp',
'svg',
'ico',
'raw'];

// Liste des extensions autorisées pour les fichiers PDF
$pdf_extensions = ['pdf'];

// Liste des extensions autorisées pour les fichiers de présentation (par exemple, PowerPoint)
$presentation_extensions = ['ppt', 'pptx','odp','pptm','ppsx'];

// pour les fonctions speciales comme les quiz html...
$ext_speciales = ["html"];

// Fusionner les listes en une seule liste
$ext_autorisees = array_merge($image_extensions, $pdf_extensions, $presentation_extensions,$ext_speciales);

function check_ext($filename) {
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    return in_array(strtolower($extension), $GLOBALS["ext_autorisees"]);
}


function ajouter_doc($request){

    global $conn;

    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO ensembles (commentaire_auteur,corrige_inclu,date_conception) VALUES(?,?,?)";

    try{
        $stm = $conn->prepare($sql);
        $request['commentaire_auteur'] = htmlspecialchars($request["commentaire_auteur"]);
        $request["corrige_inclu"] = boolval($request["corrige_inclu"]);
        $request["date_conception"] = htmlspecialchars($request["date_conception"]);
        $stm->bind_param("sis",$request['commentaire_auteur'],$request["corrige_inclu"],$request["date_conception"]);
        $stm->execute();
        //$conn->execute_query($sql,array(htmlspecialchars($request['commentaire_auteur']),boolval($request["corrige_inclu"])));
        
        saveFilesFromPost($request,mysqli_insert_id($conn));
    }catch(Exception $e){
        echo(json_encode(["status"=>"0","msg"=>$e->getMessage()]));
    }

}

function saveFilesFromPost($postData,$id_ensemble) {

    global $conn;
    

    // Check if the $_POST variable is set and contains files
    //echo(print_r($_FILES,true));

    if (isset($_FILES) && is_array($_FILES)) {

        
        
        // Iterate through each file in the $_FILES array

        $safe_type = intval($postData['type']);


        $i = 0;
        //var_dump($_FILES);


        foreach ($_FILES as $file) {
            // Extract file information
            if (isset($file['name'])){
                $fileName = htmlspecialchars($file['name']);
                if(!check_ext($fileName)){
                    echo(json_encode(["status"=>"0","msg"=>"Error saving file '$uniqueFileName'"]));
                    exit;
                }

            }else{
                echo("WTFFF");
                print_r($file);
            }

            // Create a unique filename to avoid overwriting existing files
            $uniqueFileName = uniqid() . '_' . $fileName;

            // Define the path to save the file
            $filePath = $GLOBALS['uploadDir'] . $uniqueFileName;

            //echo($filePath."\n");
            
            
            
            // Save the file
            $f = fopen($file['tmp_name'],"r");
            //echo fread($f,filesize($file['tmp_name']));
            fclose($f);


            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                echo(json_encode(["status"=>"1","msg" =>"File '$uniqueFileName' has been saved successfully."]));
            } else {
                echo(json_encode(["status"=>"0","msg"=>"Error saving file '$uniqueFileName'"]));
                exit;

            }

                

            try{
            //update the database
            $safe_titre = htmlspecialchars($postData['titre']);

            global $max_val_type;

            if ($safe_type < 1 || $safe_type > $max_val_type) {
                echo(json_encode(['status'=> '2','msg'=>"Le type de document spécifié n'existe pas."]));
                // supprime donc le fichier
                unlink($filePath);

                exit;
            }

            // pour tester, pas implémenté les commentaires globaux ni les themes
            $sql="INSERT INTO documents (titre,type,upload_path,commentaire_auteur,ensemble_id) VALUES(?,?,?,?,?)";
            $conn->execute_query($sql,array($safe_titre,$safe_type,"archives/".$uniqueFileName,$postData['commentaire_doc_'.$i],$id_ensemble));
            }catch(Exception $e){
                echo(json_encode(['status'=> '0','msg'=>$e->getMessage()]));
                //exit;
            }



            $i ++;

        }



        // enregistrement des exercices dans le cas d'une annale
        if($safe_type == 1){
            
            $exercices = json_decode($postData['exercices'],true);
            $document_id = mysqli_insert_id($conn);
            foreach ($exercices as $key => $ex) {
                // premièrement, on enregistre l'exercice
                $sql= 'INSERT INTO exercices (commentaire_auteur,ensemble_id,document_id,duree) VALUES(?,?,?,?)';
                $conn->execute_query($sql,array($ex["commentaire_exo"],$id_ensemble,$document_id,intval($ex["duree"])));

                $id_exo = mysqli_insert_id($conn);

                // on recherche pour chaque thème s'il n'existe pas déjà,
                // si non, on en créer un nouveau
                foreach($ex["themes"] as $theme){

                    // pour l'instant un match complet mais on va essayer d'ameliorer ça avec
                    // des regex
                    $sql= "SELECT id FROM themes WHERE name=\"".htmlspecialchars($theme)."\"";
                    $result = $conn->execute_query($sql);
                    if ($result){
                        if (mysqli_num_rows($result) > 0) {
                            $row = mysqli_fetch_assoc($result);
                            $id_theme = $row["id"];
                        }else{
                            echo("creation d'un theme");
                            $sql = "INSERT INTO themes (name) VALUES(?)";
                            $conn->execute_query($sql,array($theme));

                            $id_theme = mysqli_insert_id($conn);
                            
                        }

                        // ensuite, on enregistre les qui lui sont associés
                        $sql= 'INSERT INTO exercices_themes (exercice_id,ensemble_id,theme_id) VALUES(?,?,?)';
                        $result = $conn->execute_query($sql,array($id_exo,$id_ensemble,$id_theme));
                        echo("enregistrement d'un exercice");
                    }
                }





            }

        }




    } else {
        echo(json_encode(["status"=>"2","msg"=>"No files in the POST data."]));
        exit;
    }
}

function RechercheExercices($query, $length, $tags)
{
    global $conn;

    // Build the SQL query based on the search parameters
    $sql = "SELECT * FROM documents AS d INNER JOIN ensembles AS e ON d.ensemble_id = e.id WHERE e.valide=TRUE ";

    $conditions = [];

    if (!empty($query)) {

        // va essayer de retrouver tout les mots de la requête dans le titre
        $query = htmlspecialchars($query);
        $query_words = preg_split("[ ]",$query);

        foreach ($query_words as $word) {
            $conditions[] = "AND titre LIKE '%$word%'";
        }
    }

    if (!empty($length)) {
        $conditions[] = "duree = $length";
    }

    if (!empty($tags)) {
        $tagConditions = array_map(function ($tag) {
            $tag = htmlspecialchars($tag);
            return "EXISTS (SELECT * FROM exercices_themes AS et INNER JOIN themes AS t ON et.exercice_id = t.id WHERE et.theme_id = t.id AND t.name = '$tag')";
        }, $tags);

        $conditions[] = implode(" AND ", $tagConditions);
    }



    $sql .= implode(" AND ", $conditions);
    //echo $sql;
    // Execute the query
    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Error executing search query: " . $conn->error);
    }

    $exercises = [];

    while ($row = $result->fetch_assoc()) {
        $exercises[] = $row;
    }

    $conn->close();

    return $exercises;




}



function valider_ensemble($ensembleId) {

    $sql = "UPDATE ensembles SET valide = 1 WHERE id = $ensembleId";
    global $conn;
    $conn->execute_query($sql);
}

function supprimer_ensemble($ensemble_id){

    
    global $conn;

    // premièrement, enlever tout les documents téléversés appartenant à l'ensemble
    $sql = "SELECT upload_path FROM documents WHERE ensemble_id=?";
    $res = $conn->execute_query($sql,array($ensemble_id));

    while($tmp=$res->fetch_assoc()){
        unlink($tmp["upload_path"]);
    }

    // deuxièmement, supprimer toutes les traces de l'ensemble dans la bdd
    $sql = "DELETE FROM exercices_themes WHERE ensemble_id=$ensemble_id";
    $conn->execute_query($sql);
    $sql = "DELETE FROM exercices WHERE ensemble_id=$ensemble_id";
    $conn->execute_query($sql);
    $sql = "DELETE FROM documents WHERE ensemble_id=$ensemble_id";
    $conn->execute_query($sql);
    $sql = "DELETE FROM ensembles WHERE id=$ensemble_id";
    $conn->execute_query($sql);
}


function generer_chronologie(){

    global $conn;

    // on va choper les 10 derniers trucs televerses par les gens
    $sql = "SELECT * FROM ensembles WHERE valide=1 ORDER BY date_televersement DESC ";

    $res = $conn->execute_query($sql);
    $i = 0;
    $ensembles = array();
    while (($ens = $res->fetch_assoc()) && $i < 10){

        array_push($ensembles,$ens);

        $i++;
    }

    // on rajoute le chemin vers chaque document présent dans l'ensemble
    $resultat_complet = array();
    foreach($ensembles as $ens){
        $sql = "SELECT titre,upload_path,ensemble_id FROM documents WHERE ensemble_id=?";
        $res = $conn->execute_query($sql,array($ens["id"]));
        $ens["documents"] = array();
        while($doc = $res->fetch_assoc()){
            array_push($ens["documents"],$doc);
        }

        array_push($resultat_complet,$ens);

    }


    return $resultat_complet;
}

?>
