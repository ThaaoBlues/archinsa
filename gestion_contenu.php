<?php

// Check if user is logged in and is an admin
if (!isset($_SESSION["utilisateur_authentifie"]) || $_SESSION["utilisateur_authentifie"] !== true || !$_SESSION["admin"]) {
    header("Location: index.php");
    exit;
}

// Database Connection
include("test_creds.php");

$mysqli = new mysqli($servername, $db_username, $db_password,$dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle Update for Ensembles
if (isset($_POST['update_ensemble'])) {
    $id = $_POST['ensemble_id'];
    $commentaire_auteur = $_POST['commentaire_auteur'];
    $valide = isset($_POST['valide']) ? 1 : 0;
    $corrige_inclu = isset($_POST['corrige_inclu']) ? 1 : 0;
    $date_conception = $_POST['date_conception'];
    $id_auteur = $_POST['id_auteur'];

    $stmt = $mysqli->prepare("UPDATE ensembles SET commentaire_auteur = ?, valide = ?, corrige_inclu = ?, date_conception = ?, id_auteur = ? WHERE id = ?");
    $stmt->bind_param('siisii', $commentaire_auteur, $valide, $corrige_inclu, $date_conception, $id_auteur, $id);
    $stmt->execute();
    $stmt->close();
}

// Handle Update for Documents
if (isset($_POST['update_document'])) {
    $id = $_POST['document_id'];
    $titre = $_POST['titre'];
    $type = $_POST['type'];
    $commentaire_auteur = $_POST['commentaire_auteur'];

    echo var_dump($_POST);

    $stmt = $mysqli->prepare("UPDATE documents SET titre = ?, type = ?, commentaire_auteur = ? WHERE id = ?");
    $stmt->bind_param('sisi', $titre, $type, $commentaire_auteur, $id);
    $stmt->execute();
    $stmt->close();
}

// Handle Delete Document
if (isset($_GET['delete_document'])) {
    $id = (int)$_GET['id'];
    $path = $_GET['path'];

    if (file_exists($path)) {
        unlink($path); // Remove file
    }

    $stmt = $mysqli->prepare("DELETE FROM documents WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");
}

// Fetch Ensembles
$ensembles = $mysqli->query("SELECT * FROM ensembles")->fetch_all(MYSQLI_ASSOC);

// Fetch Documents
$documents = $mysqli->query("SELECT * FROM documents")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ensembles & Documents Dashboard</title>
</head>
<body>

<h2>Manage Ensembles</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Commentaire Auteur</th>
        <th>Valide</th>
        <th>Corrige Inclu</th>
        <th>Date Conception</th>
        <th>Auteur ID</th>
        <th>Action</th>
    </tr>
    <?php foreach ($ensembles as $ensemble): ?>
    <tr>
        <form method="POST">
            <td><?php echo $ensemble['id']; ?></td>
            <td><input type="text" name="commentaire_auteur" value="<?php echo $ensemble['commentaire_auteur']; ?>"></td>
            <td><input type="checkbox" name="valide" <?php echo $ensemble['valide'] ? 'checked' : ''; ?>></td>
            <td><input type="checkbox" name="corrige_inclu" <?php echo $ensemble['corrige_inclu'] ? 'checked' : ''; ?>></td>
            <td><input type="text" name="date_conception" value="<?php echo $ensemble['date_conception']; ?>"></td>
            <td><input type="number" name="id_auteur" value="<?php echo $ensemble['id_auteur']; ?>"></td>
            <td>
                <input type="hidden" name="ensemble_id" value="<?php echo $ensemble['id']; ?>">
                <input type="submit" name="update_ensemble" value="Update">
            </td>
        </form>
        
    </tr>
    <?php endforeach; ?>
</table>

<h2>Manage Documents</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Titre</th>
        <th>Type</th>
        <th>Upload Path</th>
        <th>Commentaire Auteur</th>
        <th>Ensemble ID</th>
        <th>Theme ID</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($documents as $document): ?>
    <tr>
        <form method="POST">
        <td><?php echo $document['id']; ?></td>
        <td><input type="text" name="titre" value="<?php echo $document['titre']; ?>"></td>
        <td><input type="number" name="type" value="<?php echo $document['type']; ?>"></td>
        <td><?php echo $document['upload_path']; ?></td>
        <td><input type="text" name="commentaire_auteur" value="<?php echo $document['commentaire_auteur']; ?>"></td>
        <td><input type="number" name="ensemble_id" value="<?php echo $document['ensemble_id']; ?>"></td>
        <td><input type="number" name="theme_id" value="<?php echo $document['theme_id']; ?>"></td>
        <td>
            <input type="hidden" name="document_id" value="<?php echo $document['id']; ?>">
            <input type="submit" name="update_document" value="Update">
            <a href="?delete_document=1&id=<?php echo $document['id']; ?>&path=<?php echo $document['upload_path']; ?>" onclick="return confirm('Are you sure you want to delete this document?')">Delete</a>
        </td>
        </form>

    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
