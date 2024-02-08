
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
    <button id="btn-connection">connection</button>
    <button id="lien-deconnection">déconnection</button>

    <div id="user_status">

    </div>

    <form id="recherche_form">
        <input type="text" id="recherche_input" placeholder="Rechercher une fiche, annale ...">
        <input type="text" id="themes_input" placeholder="themes (appuyez sur la touche entrée entre chaque thèmes)">
        <input type="number" id="duree_input" placeholder="durée en minutes">
    </form>

    <a href="televerser.php">Téléverser des documents</a>


    <div id="liste_resultats">
    </div>

</body>
<?php
    include "_partials/_footer.php";
?>
</html>
