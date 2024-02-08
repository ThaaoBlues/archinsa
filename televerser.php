<!DOCTYPE html>
<html lang="en">
<?php
    $titre_page = "Téléverser sur Arch'INSA";
    include "_partials/_head.php";
?>
<body>
<?php
include("session_verif.php");
// Include the PHP-CSRF library
include('php-csrf.php');

$csrf = new CSRF();
?>

<!-- Input to choose files -->

<form id="uploadForm" enctype="multipart/form-data">
<input type="file" id="fileInput" multiple>
<br>
<input type="text" placeholder="titre" id="titre"></input>
<label for="titre">N'hésitez pas à bien mettre 1A, 2A, ... et la matière concernée dans le titre.</label>
<br>
<select id="select_type">
    <option value="1" >annale</option>
    <option value="2" >fiche_revision</option>
    <option value="3" >HTML personnalisé</option>
</select>

<input type="text" placeholder="commentaires généraux sur l'ensemble des documents" id="commentaire_auteur"></input>
<br>
<div id="selectedImages"></div>

<div id="corrige_checkbox_wrapper">
    <input type="checkbox" id="corrige_checkbox">
    <label for="corrige_checkbox">Corrigé inclu</label>
</div>

<input type="date" id="date_conception_input">
<label for="date_conception_input">Date de conception du/des documents (Mettez juste la bonne année si vous ne savez pas) </label>
<br>
<button type="button" id="btn-soumettre">Téléverser les fichiers</button>
</form>

<div id="exercices_details_wrapper">
    <button id="btn-details-exo">Ajouter les détails d'un exercice</button>

</div>
<button id="btn-camera">Prendre des photos</button>

</body>

<?php
    echo $csrf->script($context='televersement', $name='jeton_csrf', $declaration='var', $time2Live=-1, $max_hashes=5);
    include "_partials/_footer.php";
?>
</html>
