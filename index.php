
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

    <h4>Comme vous pouvez le constater, on cherche quelqu'un pour le design (html + css) du site :D club.info@amicale-insat.fr</h4>

    <a href="inscription.php" class="button color-red-tr" id="btn-connection">S'inscrire</a>
    <a href="connection.php" class="button color-red-tr" id="btn-connection">Se connecter</a>
    <a href="deconnection.php" class="button color-red-tr" id="btn-deconnection">Se déconnecter</a>
    <br>
    <br>
    <div id="user_status">
        <?php
            if(isset($_SESSION["utilisateur_authentifie"]) && ($_SESSION["utilisateur_authentifie"] == 1)){
                ?><h2>Salut <?= $_SESSION["username"] ?> !</h2><?php
            }else{
                ?><h2>Vous n'êtes pas connecté !</h2><?php
            }
        ?>
    </div>

    <div class="barre-recherche centre-horizontal">
        <form id="recherche_form">
            <input  class="champ" type="text" id="recherche_input" placeholder="Rechercher une fiche, annale ...">
            <input hidden type="submit">
            <input hidden class="champ" type="text" id="themes_input" placeholder="themes (appuyez sur la touche entrée entre chaque thèmes)">
            <input  hidden class="champ" type="number" id="duree_input" placeholder="durée en minutes">
        </form>
    </div>

    <a href="televerser.php" class="color-red-tr floating-action-btn">
<pre>
============================================
|    _                                     |
|  _| |_                                   |
| |_   _| Téléverser des documents         |
|   |_|                                    |
============================================
</pre></a>


    <div class="centre-horizontal etaler">
        <div id="liste_resultats" class="centre-txt">
        </div>
    <div>

</body>
<?php
    include "_partials/_footer.php";
?>
</html>
