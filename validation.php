<?php
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
            echo "<p><a href='#' onclick='validateDocument({$ens_id})'>Valider l'ensembre</a></p>";
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
    echo "<p><a href='#' onclick='validateDocument({$ens_id})'>Valider l'ensembre</a></p>";
    echo "</div>";

}

// Function to validate documents in an ensemble
function valider_ensemble($ensembleId) {
    // Update the "valide" status in the "ensembles" table
    // You need to customize the SQL query based on your actual database structure
    $updateQuery = "UPDATE ensembles SET valide = 1 WHERE id = $ensembleId";
    // Execute the update query
    global $conn;
    $conn->execute_query($updateQuery);
}

// Check if the form is submitted for ensemble validation
if (isset($_POST['ensemble_id'])) {
    $ensembleId = $_POST['ensemble_id'];
    valider_ensemble($ensembleId);
}

// Include your HTML, CSS, and JavaScript for the frontend
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Validation Dashboard</title>
    <!-- Include your CSS styles here -->
</head>
<body>

<h2>Document Validation Dashboard</h2>

<!-- Display documents -->
<?php displayDocuments(); ?>

<!-- Include your JavaScript for document validation here -->
<script>
    function validateDocument(ensembleId) {
        // Send an AJAX request to validate the ensemble
        // You can use fetch or jQuery.ajax
        // Example using fetch:
        fetch('validate_ensemble.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'ensemble_id=' + ensembleId,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status == 1) {
                // oui
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
</script>

<!-- Include your HTML and CSS styles for the form to add documents here -->

</body>
</html>
