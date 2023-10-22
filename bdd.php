<?php


include("annales/test_creds.php");
$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "archivinsa";
function ajouter_doc($request){

    $conn = new mysqli($GLOBALS["servername"], $GLOBALS["username"], $GLOBALS["password"], $GLOBALS["dbname"]);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO ensemble (commentaire_auteur) VALUES(\"\")";

    
    try{
        $conn->execute_query($sql,array("",));
        saveFilesFromPost($request,mysqli_insert_id($conn),$conn);
    }catch(Exception $e){
        echo(json_encode(["status"=>"0","msg"=>$e]));
    }

}

function saveFilesFromPost($postData,$id_ensemble,$conn) {
    // Check if the $_POST variable is set and contains files
    if (isset($postData['files']) && is_array($postData['files'])) {
        // Directory to save the files
        $uploadDir = 'archives/';

        // Iterate through each file in the $_POST['files'] array
        foreach ($postData['files'] as $file) {
            // Extract file information
            $fileName = $file['name'];
            $fileData = $file['data'];

            // Decode base64 encoded file data
            $fileData = base64_decode($fileData);

            // Create a unique filename to avoid overwriting existing files
            $uniqueFileName = uniqid() . '_' . $fileName;

            // Define the path to save the file
            $filePath = $uploadDir . $uniqueFileName;

            // Save the file
            if (file_put_contents($filePath, $fileData) !== false) {
                echo(json_encode(["status"=>"1","msg" =>"File '$uniqueFileName' has been saved successfully."]));
            } else {
                echo(json_encode(["status"=>"0","msg"=>"Error saving file '$uniqueFileName'"]));
            }

            //update the database
            $safe_titre = htmlspecialchars($postData['titre']);
            $safe_type = htmlspecialchars($postData['type']);

            // pour tester, pas implémenté les commentaires globaux ni les themes
            $sql="INSERT INTO documents (titre,type,upload_path,commentaire_auteur,ensemble_id) VALUES(?,?,?,?,?)";
            $conn->execute_query($sql, array("titre"=> $safe_titre,"type"=>$safe_type,"upload_path"=> $uploadDir,"commentaire_auteur"=>"","ensemble_id"=>$id_ensemble));

        }
    } else {
        echo(json_encode(["status"=>"2","msg"=>"No files in the POST data."]));
    }
}

?>