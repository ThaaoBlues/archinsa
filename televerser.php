<!DOCTYPE html>
<html lang="en">
<?php
    $titre_page = "Téléverser sur Arch'INSA";
    include "_partials/_head.php";
?>
<body>
<?php
include("session_verif.php");
include('php-csrf.php');

$csrf = new CSRF();
?>

<div class="centre-horizontal bulle-rouge" id="titre">
    <pre class="centre-txt gros-titre">
   __    ____   ___  _   _ /'/ ____  _  _  ___    __   
  /__\  (  _ \ / __)( )_( )   (_  _)( \( )/ __)  /__\  
 /(__)\  )   /( (__  ) _ (     _)(_  )  ( \__ \ /(__)\ 
(__)(__)(_)\_) \___)(_) (_)   (____)(_)\_)(___/(__)(__)
    </pre>

</div>


<div class="formulaire">
    <form id="uploadForm" enctype="multipart/form-data">
    <input type="file" class="champ" id="fileInput" multiple>
    <br>
    <input type="text" class="champ" placeholder="titre" id="titre"></input>
    <label for="titre" class="champ" >N'hésitez pas à bien mettre 1A, 2A, ... et la matière concernée dans le titre.</label>
    <br>
    <select id="select_type" class="champ" >
        <option value="1" >Annale</option>
        <option value="2" >Fiche de révision</option>
        <option value="3" >HTML personnalisé</option>
    </select>

    <input type="text" class="champ" placeholder="commentaires généraux sur l'ensemble des documents" id="commentaire_auteur"></input>
    <br>
    <div id="selectedImages" class="champ"></div>

    <div id="corrige_checkbox_wrapper">
        <input type="checkbox" class="champ" id="corrige_checkbox">
        <label for="corrige_checkbox" class="champ">Corrigé inclu</label>
    </div>

    <input type="date" id="date_conception_input" class="champ" >
    <label for="date_conception_input" class="champ" >Date de conception du/des documents (Mettez juste la bonne année si vous ne savez pas) </label>
    <br>
    <button type="button" id="btn-soumettre" class="champ button color-green-tr" >Téléverser les fichiers</button>
    </form>

    <div id="exercices_details_wrapper">
        <button id="btn-details-exo" class="champ" >Ajouter les détails d'un exercice</button>

    </div>

</div>

    <button id="btn-camera" class="color-red-tr floating-action-btn" >
    <pre>    _   
  _| |_ 
 |_   _| Prendre des photos
   |_|   
</pre></button>
</body>

<?php
    echo $csrf->script($context='televersement', $name='jeton_csrf', $declaration='var', $time2Live=-1, $max_hashes=5);
    include "_partials/_footer.php";
?>
</html>
