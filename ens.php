<?php
include("session_verif.php");
include("test_creds.php");


try {
    $conn = new mysqli($servername, $username, $password,$dbname);
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}

// Récupération de l'ID de l'ensemble et du thème depuis l'URL ou autrement
$ensembleId = isset($_GET['ensemble_id']) ? intval($_GET['ensemble_id']) : '';
$themeId = isset($_GET['theme_id']) ? intval($_GET['theme_id']) : '';

// Vérification de la validité de l'ID de l'ensemble
if (!empty($ensembleId)) {
    // Préparation de la requête SQL pour obtenir les informations sur l'ensemble
    $sqlEnsemble = 'SELECT * FROM ensembles WHERE id = ?';
    $stmtEnsemble = $conn->prepare($sqlEnsemble);
    $stmtEnsemble->bind_param('i', $ensembleId);
    $stmtEnsemble->execute();
    $resultEnsemble = $stmtEnsemble->get_result();
    $ensemble = $resultEnsemble->fetch_assoc();

    if ($ensemble && $ensemble['valide'] == true) {
        // Affichage de l'intitulé de l'ensemble
        echo '<h1>' . htmlspecialchars($ensemble['commentaire_auteur']) . '</h1>';

       
        // Préparation de la requête SQL pour obtenir les informations sur les exercices sélectionnés
        $sqlExos = 'SELECT e.*, t.name AS theme_name, te.id AS exotheme_id FROM exercices e '.
                   'JOIN themes t ON e.ensemble_id = ?'.
                   'LEFT JOIN exercices_themes te ON e.id = te.exercice_id ORDER BY te.id ASC';
        $stmtExos = $conn->prepare($sqlExos);
        echo($sqlExos);
        $stmtExos->bind_param('i', $ensembleId);
        $stmtExos->execute();
        $resultExos = $stmtExos->get_result();

        while ($exo = $resultExos->fetch_assoc()) {
            switch ($exo['type']) {
                case 1:
                    // Traiter les annales
                    echo '<div class="document">';
                    echo '<h2>' . htmlspecialchars($exo['titre']) . '</h2>';
                    echo '<p>' . nl2br(htmlspecialchars($exo['commentaire_auteur'])) . '</p>';
                    echo '<p>Durée estimée : ' . gmdate('H:i:s', $exo['duree']) . '</p>';
                    echo '<a href="' . htmlspecialchars($exo['upload_path']) . '" target="_blank">Télécharger</a>';
                    echo '</div>';
                    break;
                case 2:
                    // Traiter les textes à trous
                    break;
                case 3:
                    // Traiter les fiches de révision
                    echo '<div class="document">';
                    echo '<h2>' . htmlspecialchars($exo['titre']) . '</h2>';
                    echo '<p>' . nl2br(htmlspecialchars($exo['commentaire_auteur'])) . '</p>';
                    echo '<a href="' . htmlspecialchars($exo['upload_path']) . '" target="_blank">Télécharger</a>';
                    echo '</div>';
                    break;
                case 4:
                    // Traiter les QCM
                    echo "oui";
                    break;
            }
        }
    } else {
        echo 'L\'ensemble demandé n\'existe pas ou il n\'est pas encore validé.';
    }
} else {
    echo 'Aucun identifiant d\'ensemble fourni.';
}

// Fermeture de la connexion à la base de données
$conn->close();
?>


?>