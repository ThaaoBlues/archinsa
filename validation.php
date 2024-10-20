<?php

include('php-csrf.php');

session_start();

$csrf = new CSRF();

// Check if user is logged in and is an admin
if (!isset($_SESSION["utilisateur_authentifie"]) || $_SESSION["utilisateur_authentifie"] !== true || !$_SESSION["admin"]) {
    header("Location: index.php");
    exit;
}
include("test_creds.php");

$conn = new mysqli($servername, $db_username, $db_password,$dbname);


// Function to fetch and display documents
function generer_chronologie() {

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
            echo "<p><a href='#' onclick='valider_ensemble({$ens_id})' class='lien-valider-ens'>Valider l'ensemble</a></p>";
            echo "<p><a href='#' onclick='supprimer_ensemble({$ens_id})' class='lien-supp-ens'>Supprimer l'ensemble</a></p>";
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

        generateFileHTML($row);

    }


    // complète le formulaire du dernier ensemble itéré
    echo "<p><a href='#' onclick='valider_ensemble({$ens_id})' class='lien-valider-ens' id_ens='$ens_id' >Valider l'ensemble</a></p>";
    echo "<p><a href='#' onclick='supprimer_ensemble({$ens_id})' class='lien-supp-ens' id_ens='$ens_id'>Supprimer l'ensemble</a></p>";

    echo "</div>";

}


// Function to handle different file types and generate HTML dynamically
function generateFileHTML($row) {
    // Simulating the switch-case equivalent in PHP using a switch on doc.type
    $doc_type = $row['type']; // Assuming 'type' is the same as doc.type in JS

    switch ($doc_type) {
        case 2: // Image
            // Create image element
            echo "<img src=\"{$row['upload_path']}\" alt=\"{$row['titre']}\" />";
            
            // Create link to view image
            echo "<a href=\"{$row['upload_path']}\" class=\"lien\" target=\"_blank\">Voir image</a>";
            break;

        case 3: // PDF
            // Create embed for PDF
            echo "<embed src=\"{$row['upload_path']}\" type=\"application/pdf\" width=\"100%\" height=\"600px\" />";
            
            // Create link to view PDF
            echo "<a href=\"{$row['upload_path']}\" class=\"lien\" target=\"_blank\">Voir PDF en grand</a>";
            break;

        case 4: // Video
            // Create video element with controls
            echo "<video src=\"{$row['upload_path']}\" controls></video>";
            break;

        case 5: // HTML
            // Create iframe for HTML document
            echo "<iframe src=\"{$row['upload_path']}\" width=\"100%\" height=\"600px\"></iframe>";
            break;

        case 1: // Plain Text
            // Fetch content via PHP file_get_contents
            $text = file_get_contents($row['upload_path']);
            echo "<textarea readonly style=\"width: 100%; height: 200px;\">$text</textarea>";
            break;

        default:
            // Unsupported file type, create link
            echo "<a href=\"{$row['upload_path']}\" class=\"lien\" target=\"_blank\">Type de fichier non supporté.</a>";
            break;
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<?php
    $titre_page = "Validation des documents";
    include "_partials/_head.php";
?>
<body>

<h2>Validation des documents</h2>

<?php generer_chronologie(); ?>

</body>
<?php
    echo $csrf->script($context='supprimer_ensemble', $name='jeton_supprimer_ensemble', $declaration='var', $time2Live=-1, $max_hashes=5);
    echo $csrf->script($context='valider_ensemble', $name='jeton_valider_ensemble', $declaration='var', $time2Live=-1, $max_hashes=5);

    include "_partials/_footer.php";
?>
</html>
