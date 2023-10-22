<?php

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


function ajouter_doc($request){
    
    saveFilesFromPost($request);

    if (isset($request['files']) && is_array($request['files'])) {
        foreach ($request['files'] as $file) {
            $sql="INSERT INTO ";
        }
    }

}

function saveFilesFromPost($postData) {
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
        }
    } else {
        echo(json_encode(["status"=>"2","msg"=>"No files in the POST data."]));
    }
}

?>