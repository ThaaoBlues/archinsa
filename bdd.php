<?php


include("test_creds.php");
include_once("utils/token.php");

$conn = new mysqli($servername, $db_username, $db_password,$dbname);


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

    $sql = "INSERT INTO ensembles (commentaire_auteur,corrige_inclu,date_conception,id_auteur) VALUES(?,?,?,?)";

    try{
        $stm = $conn->prepare($sql);
        $request['commentaire_auteur'] = htmlspecialchars($request["commentaire_auteur"]);
        $request["corrige_inclu"] = boolval($request["corrige_inclu"]);
        $request["date_conception"] = htmlspecialchars($request["date_conception"]);
        $stm->bind_param("sisi",$request['commentaire_auteur'],$request["corrige_inclu"],$request["date_conception"],$_SESSION["user_id"]);
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


            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
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
                            //echo("creation d'un theme");
                            $sql = "INSERT INTO themes (name) VALUES(?)";
                            $conn->execute_query($sql,array($theme));

                            $id_theme = mysqli_insert_id($conn);
                            
                        }

                        // ensuite, on enregistre les qui lui sont associés
                        $sql= 'INSERT INTO exercices_themes (exercice_id,ensemble_id,theme_id) VALUES(?,?,?)';
                        $result = $conn->execute_query($sql,array($id_exo,$id_ensemble,$id_theme));
                        //echo("enregistrement d'un exercice");
                    }
                }





            }

        }


    echo(json_encode(["status"=>"1","msg" =>"Files has/have been saved successfully."]));


    } else {
        echo(json_encode(["status"=>"2","msg"=>"No files in the POST data."]));
        exit;
    }
}

function RechercheExercices($query, $length, $tags, $tout_les_insa)
{
    global $conn;

    // Start with the base SQL query
    $sql = "SELECT * FROM documents AS d INNER JOIN ensembles AS e ON d.ensemble_id = e.id JOIN users as u ON u.id=e.id_auteur WHERE e.valide=TRUE";

    // Array to hold the parameters
    $params = [];
    $types = "";  // Types for the bind_param function

    // Handle the INSA restriction
    if (!$tout_les_insa) {
        $sql .= " AND u.nom_insa = ?";
        $params[] = $_SESSION["nom_insa"];
        $types .= "s";  // Assuming nom_insa is a string
    }

    // Handle the search query
    if (!empty($query)) {
        $query_words = preg_split("/\s+/", htmlspecialchars($query));
        foreach ($query_words as $word) {
            $sql .= " AND titre LIKE ?";
            $params[] = "%$word%";
            $types .= "s";
        }
    }

    // Handle the length filter
    if (!empty($length)) {
        $sql .= " AND duree = ?";
        $params[] = $length;
        $types .= "i";  // Assuming duree is an integer
    }

    // Handle the tags filter
    if (!empty($tags)) {
        foreach ($tags as $tag) {
            $tag = htmlspecialchars($tag);
            $sql .= " AND EXISTS (SELECT * FROM exercices_themes AS et INNER JOIN themes AS t ON et.exercice_id = t.id WHERE et.theme_id = t.id AND t.name = ?)";
            $params[] = $tag;
            $types .= "s";
        }
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        throw new Exception("Error preparing the query: " . $conn->error);
    }

    // Bind the parameters dynamically
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    // Execute the query
    if (!$stmt->execute()) {
        throw new Exception("Error executing the search query: " . $stmt->error);
    }

    // Fetch the results
    $result = $stmt->get_result();
    $exercises = [];

    while ($row = $result->fetch_assoc()) {
        $exercises[] = $row;
    }

    // Clean up
    $stmt->close();
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

function connecter_utilisateur($username,$password){

    global $conn;

    $ret = 0;

    $stmt = $conn->prepare("SELECT id,password_hash,admin,nom_insa FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {

        $stmt->bind_result($id,$password_hash,$admin,$nom_insa);
        $ret = $stmt->fetch();

        if (password_verify($password, $password_hash)) {
            $_SESSION["utilisateur_authentifie"] = true;
            $_SESSION["username"] = $username;
            $_SESSION["admin"] = $admin;
            $_SESSION["nom_insa"] = $nom_insa;
            $_SESSION["user_id"] = $id;
            $ret = 1;
        } else {
            $ret = 0;
        }
    } else {
        $ret = 0;
    }

    $stmt->close();

    if($ret){
        $ret=verifier_utilisateur($id);
    }
    return $ret;
}


function inscription_utilisateur($username,$password_hash,$nom_insa){

    global $conn;

    if(!in_array($nom_insa,["insa_toulouse","insa_lyon","insa_rennes","insa_cvl","insa_hdf","insa_rouen","insa_strasbourg","insa_hdf"])){
        $ret = 0;
        return $ret;
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password_hash,nom_insa) VALUES (?, ?,?)");
    $stmt->bind_param("sss", $username, $password_hash,$nom_insa);
    
    $ret = $stmt->execute();
    
    $stmt->close();


    $tok = new Token();
    $user_id = mysqli_insert_id($conn);
    $tok->Add($user_id);

    /*
    if($ret){
        // met le statut de l'utilisateur à connecté pour lui eviter de se connecter just après l'inscription
        $_SESSION["utilisateur_authentifie"] = true;
        $_SESSION["username"] = $username;
        $_SESSION["admin"] = 0;
        $_SESSION["nom_insa"] = $nom_insa;
        $_SESSION["user_id"] = $conn->insert_id;
    }*/

    if($ret){
        return $tok->getToken($user_id);
    }else{
        return "[ERREUR]";

    }

}


function verifier_utilisateur($token){
    global $conn;

    $ret = 0;

    $t_instance = new Token();
	
    $user_id = $t_instance->getUserID($token);

    if($t_instance->isValid($user_id, $token) && $user_id != -1) {
		$t_instance->delete($user_id, $token);
        $stmt = $conn->prepare("UPDATE users SET verifie=? WHERE id = ?");
        $val=1;
        $stmt->bind_param("ss",$val,$user_id);
        $ret = $stmt->execute();
        $stmt->close();    
	}

    return $ret;
}

function utilisateur_est_verifie($user_id){
    global $conn;
    $stmt = $conn->prepare("SELECT verifie FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $stmt->store_result();

    $ret = $stmt->num_rows > 0;
    $verif = 0;
    if($ret){
        $stmt->bind_result($verif);
        $ret = $stmt->fetch();
        $stmt->close();
    }

    return $ret && ($verif == 1);
}

?>
