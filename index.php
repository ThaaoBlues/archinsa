
<!DOCTYPE html>
<html lang="en">
<?php
    $titre_page = "Arch'INSA";
    include "_partials/_head.php";
?>
<body>
<?php

    session_start();

?>

<div class="centre-horizontal bulle-rouge" id="titre">
    <pre class="centre-txt gros-titre">
   __    ____   ___  _   _ /'/ ____  _  _  ___    __   
  /__\  (  _ \ / __)( )_( )   (_  _)( \( )/ __)  /__\  
 /(__)\  )   /( (__  ) _ (     _)(_  )  ( \__ \ /(__)\ 
(__)(__)(_)\_) \___)(_) (_)   (____)(_)\_)(___/(__)(__)
    </pre>

</div>


    <button class="button color-red-tr" id="btn-connection">connection</button>
    <button class="button color-red-tr" id="btn-deconnection">déconnection</button>

    <div id="user_status">

    </div>

    <div class="barre-recherche centre-horizontal">
        <form id="recherche_form">
            <input  class="champ" type="text" id="recherche_input" placeholder="Rechercher une fiche, annale ...">
            <input hidden class="champ" type="text" id="themes_input" placeholder="themes (appuyez sur la touche entrée entre chaque thèmes)">
            <input  hidden class="champ" type="number" id="duree_input" placeholder="durée en minutes">
        </form>
    </div>

    <a href="televerser.php" class="color-red-tr floating-action-btn">
        
<pre>    _   
  _| |_ 
 |_   _| Téléverser des documents
   |_|   
</pre></a>


    <div id="liste_resultats" class="centre-txt">
    </div>

</body>
<?php
    include "_partials/_footer.php";
?>
</html>
