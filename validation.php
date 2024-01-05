<?php

include('php-csrf.php');

session_start();

$csrf = new CSRF();


include("session_verif.php");


// Include your database connection code here
include("test_creds.php");

$conn = new mysqli($servername, $username, $password,$dbname);


// Function to fetch and display documents
function displayDocuments() {

    global $conn;

    // Fetch documents associated with non-validated ensembles
    // You need to customize the SQL query based on your actual database structure
    $query = "SELECT * FROM documents 
              INNER JOIN ensembles ON documents.ensemble_id = ensembles.id
              WHERE ensembles.valide = FALSE";
    // Execute the query and fetch results
    $result = $conn->query($query);

    // Display all documents information
    // Fini le div et met le bouton uniquement 
    // quand on finit d'itérer un ensemble donné
    $ens_id = -1;
    while($row = $result->fetch_assoc()) {


        if (($row["ensemble_id"] != $ens_id) && ($ens_id != -1) ) {
            echo "<p><a href='#' onclick='valider_ensemble({$ens_id})'>Valider l'ensembre</a></p>";
            echo "</div>";
            $ens_id = $row["ensemble_id"];
        }

        // initialisation pour la première itération
        if ($ens_id == -1){
            $ens_id = $row["ensemble_id"];
        }

        echo "<div>";
        echo "<h3>{$row['titre']}</h3>";
        echo "<p>Type: {$row['type']}</p>";
        echo "<p>Upload Path: {$row['upload_path']}</p>";
        echo "<p>Ensemble ID: {$row['ensemble_id']}</p>";

        $extension = pathinfo($row['upload_path'], PATHINFO_EXTENSION);

        if (strtolower($extension) === 'pdf'):
            echo "<embed src=\"{$row['upload_path']}\" type=\"application/pdf\" width=\"100%\" height=\"600px\" />";
        elseif (in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])):
            echo "<img src=\"{$row['upload_path']}\">";
        else:
            echo "<p>Unsupported file type</p>".$row['upload_path'];
        endif;

        echo "<p>Theme ID: {$row['theme_id']}</p>";

    }


    // complète le formulaire du dernier ensemble itéré
    echo "<p><a href='#' onclick='valider_ensemble({$ens_id})'>Valider l'ensemble</a></p>";
    echo "<p><a href='#' onclick='supprimer_ensemble({$ens_id})'>Supprimer l'ensemble</a></p>";

    echo "</div>";

}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation des documents</title>
    <!-- Include your CSS styles here -->
</head>
<body>

<h2>Validation des documents</h2>

<!-- Display documents -->
<?php displayDocuments(); ?>

<script>

    function valider_ensemble(ensembleId) {

        const formData = new FormData();
        formData.append("jeton-csrf","<?=$csrf->string($context="valider_ensemble")?>");
        formData.append("ensemble_id",ensembleId);
        fetch('api.php/valider_ensemble', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status == 1) {
                alert(data.msg)
            }else{
                alert(data.msg)
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }


    function supprimer_ensemble(ensembleId) {
        const formData = new FormData();
        formData.append("jeton-csrf","<?=$csrf->string($context="supprimer_ensemble")?>");
        formData.append("ensemble_id",ensembleId);
        
        fetch('api.php/supprimer_ensemble', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status == 1) {
                alert(data.msg)
            }else{
                alert(data.msg)
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>


</body>
</html>
