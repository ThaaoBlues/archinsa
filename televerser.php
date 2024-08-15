<!DOCTYPE html>
<html lang="en">
<?php
    $titre_page = "Téléverser sur Arch'INSA";
    include "_partials/_head.php";
?>
<body>
<?php
include('php-csrf.php');

session_start();
if (!isset($_SESSION["utilisateur_authentifie"]) || $_SESSION["utilisateur_authentifie"] !== true) {
    header("Location: index.php");
    exit;
}

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
    <label for="select-type" class="champ" >Type de ressources</label>
    <select id="select_type" class="champ" >
        <option value="1" >Annale</option>
        <option value="2" >Fiche de révision</option>
        <option value="3" >HTML personnalisé</option>
    </select>
    <br>
    <br>
    <form id="uploadForm" enctype="multipart/form-data">
    <input type="file" class="champ" id="fileInput" multiple>
    <br>
    <br>

    <label for="titre-cours" class="champ" >Nom du cours</label>
    <input type="text" class="champ-titre" placeholder="titre du cours" id="titre-cours" required></input>
    <br>
    <br>

    <label for="nb-cc" class="champ" >Numéro du CC</label>
    <input type="number" class="champ-titre" placeholder="n° du CC" id="nb-cc" required></input>
    <br>
    <br>
    <label for="nb-classe" class="champ" >Numéro de votre année (1A,2A...)</label>
    <input type="number" max="5" min="1" class="champ-titre" placeholder="classe" id="nb-annee" required></input>
    <br>
    <br>
    <label for="nom-spe" class="champ" >Nom de PO/Spécialité</label>
    <input type="text" class="champ-titre" placeholder="classe" id="nom-spe" required></input>

    <br>
    <br>
    <label for="commentaire_auteur" class="champ" >commentaires généraux sur l'ensemble des documents</label>
    <input type="text" class="champ-titre" placeholder="commentaires généraux sur l'ensemble des documents" id="commentaire_auteur"></input>
    <br>
    <br>
    <div id="selectedImages" class="champ"></div>
    <div id="corrige_checkbox_wrapper">
        <input type="checkbox" class="champ" id="corrige_checkbox">
        <label for="corrige_checkbox" class="champ">Corrigé inclu</label>
    </div>
    <br>

    <input type="date" id="date_conception_input" class="champ" >
    <label for="date_conception_input" class="champ" >Date de conception du/des documents (Mettez juste la bonne année si vous ne savez pas) </label>
    <br>
    <br>

    <button type="button" id="btn-soumettre" class="champ button color-green-tr" >Téléverser les fichiers</button>
    </form>
    <br>
    <br>

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
