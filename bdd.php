<?php


include("annales/test_creds.php");
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "archivinsa";


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

// Fusionner les listes en une seule liste
$ext_autorisees = array_merge($imageExtensions, $pdfExtensions, $presentationExtensions);

function check_ext($filename) {
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    return in_array(strtolower($extension), $GLOBALS["ext_autorisees"]);
}


function ajouter_doc($request){

    $conn = new mysqli($GLOBALS["servername"], $GLOBALS["username"], $GLOBALS["password"], $GLOBALS["dbname"]);

    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO ensemble (commentaire_auteur) VALUES(\"\")";

    
    try{
        $conn->execute_query($sql);
        saveFilesFromPost($request,mysqli_insert_id($conn),$conn);
    }catch(Exception $e){
        echo(json_encode(["status"=>"0","msg"=>$e]));
    }

}

function saveFilesFromPost($postData,$id_ensemble,$conn) {
    // Check if the $_POST variable is set and contains files
    echo(print_r($_FILES,true));
    if (isset($_FILES['fichiers']) && is_array($_FILES['fichiers'])) {
        // Directory to save the files
        // /!\ A CHANGER EN PROD /!\
        $uploadDir = '/opt/lampp/htdocs/annales/archives/';
        
        
        // Iterate through each file in the $_FILES array
        foreach ($_FILES as $file) {
            // Extract file information
            if (isset($file['name'])){
                $fileName = $file['name'];
                if(!check_ext($fileName)){
                    echo(json_encode(["status"=>"0","msg"=>"Error saving file '$uniqueFileName'"]));
                    exit;
                }

            }else{
                echo("WTFFF");
                print_r($file);
            }

            // Create a unique filename to avoid overwriting existing files
            $uniqueFileName = uniqid() . '_' . htmlspecialchars($fileName);

            // Define the path to save the file
            $filePath = $uploadDir . $uniqueFileName;

            //echo($filePath."\n");

            
            // Save the file
            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                echo(json_encode(["status"=>"1","msg" =>"File '$uniqueFileName' has been saved successfully."]));
            } else {
                echo(json_encode(["status"=>"0","msg"=>"Error saving file '$uniqueFileName'"]));
                exit;

            }

                

            try{
            //update the database
            $safe_titre = htmlspecialchars($postData['titre']);
            $safe_type = htmlspecialchars($postData['type']);

            // pour tester, pas implémenté les commentaires globaux ni les themes
            $sql="INSERT INTO documents (titre,type,upload_path,commentaire_auteur,ensemble_id) VALUES(?,?,?,?,?)";
            $conn->execute_query($sql, array("titre"=> $safe_titre,"type"=>$safe_type,"upload_path"=> $uploadDir,"commentaire_auteur"=>"","ensemble_id"=>$id_ensemble));

            }catch(Exception $e){
                echo(json_encode(['status'=> '0','msg'=>$e]));
                exit;
            }

        }


    } else {
        echo(json_encode(["status"=>"2","msg"=>"No files in the POST data."]));
        exit;
    }
}

function searchExercises($query, $length, $tags)
{
    $conn = new mysqli($GLOBALS["servername"], $GLOBALS["username"], $GLOBALS["password"], $GLOBALS["dbname"]);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Build the SQL query based on the search parameters
    $sql = "SELECT * FROM exercices";

    if (!empty($query) || !empty($length) || !empty($tags)) {
        $sql .= " WHERE";
    }

    $conditions = [];

    if (!empty($query)) {
        $conditions[] = "titre LIKE '%$query%'";
    }

    if (!empty($length)) {
        $conditions[] = "duree = $length";
    }

    if (!empty($tags)) {
        $tagConditions = array_map(function ($tag) {
            return "EXISTS (SELECT 1 FROM exercices_themes et, themes t WHERE et.exercice_id = e.id AND et.theme_id = t.id AND t.name = '$tag')";
        }, $tags);

        $conditions[] = implode(" AND ", $tagConditions);
    }

    $sql .= implode(" AND ", $conditions);

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

?>