
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

    <?php    
    if(isset($_SESSION["utilisateur_authentifie"]) && ($_SESSION["utilisateur_authentifie"] == 1)){
        ?>
            <a href="deconnection.php" class="button color-red-tr" id="btn-deconnection">Se déconnecter</a>
        <?php
    }else{
        ?>
            <a href="inscription.php" class="button color-red-tr" id="btn-connection">S'inscrire</a>
            <a href="connection.php" class="button color-red-tr" id="btn-connection">Se connecter</a>
        <?php


    }


    if(isset($_SESSION["admin"]) && ($_SESSION["admin"] == 1)){
        ?>

        <a href="validation.php" class="button color-red-tr" id="btn-validation">Validation des ensembles</a>
        <a href="utilisateurs.php" class="button color-red-tr" id="btn-validation">Gestion des utilisateurs</a>
        <a href="gestion_contenu.php" class="button color-red-tr" id="btn-validation">Gestion du contenu</a>
        
        <?php

    }?>


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


    <?php
        if(isset($_SESSION["utilisateur_authentifie"]) && ($_SESSION["utilisateur_authentifie"] == 1)){
            ?>
    <img src="img/fox-reverse.gif">

<div class="barre-recherche centre-horizontal">
        <form id="recherche_form">
            <input  class="champ" type="text" id="recherche_input" placeholder="Rechercher une fiche, annale ...">
            <div hidden>
                <label class="champ" for="tout-les-insa-switch">Activer la recherche sur tout les INSA</label>
                <input class="champ checkbox" type="checkbox" id="tout_les_insa_switch">
            </div>
            <input hidden type="submit">
            <input hidden class="champ" type="text" id="themes_input" placeholder="themes (appuyez sur la touche entrée entre chaque thèmes)">
            <input  hidden class="champ" type="number" id="duree_input" placeholder="durée en minutes">
        </form>
    </div>

    <a href="televerser.php">
<div class="ascii-art color-red-tr floating-action-btn">
============================================
|    _                                     |
|  _| |_                                   |
| |_   _| Téléverser des documents         |
|   |_|                                    |
============================================
</div></a>

    <div class="centre-horizontal etaler">
        <div id="liste_resultats" class="centre-txt">
        </div>
    <div>

            <?php

        }else{
            ?>
                <div class="centre-horizontal"> 
                    <h1>Vous devez vous connecter/inscrire avant d'accéder à Archinsa</h1>

                </div>

                <br>

                <div class="centre-horizontal"> 

                <div class="ascii-art">
    ⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣀⡀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣾⠙⠻⢶⣄⡀⠀⠀⠀⢀⣤⠶⠛⠛⡇⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢹⣇⠀⠀⣙⣿⣦⣤⣴⣿⣁⠀⠀⣸⠇⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠙⣡⣾⣿⣿⣿⣿⣿⣿⣿⣷⣌⠋⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣴⣿⣷⣄⡈⢻⣿⡟⢁⣠⣾⣿⣦⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢹⣿⣿⣿⣿⠘⣿⠃⣿⣿⣿⣿⡏⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⣀⠀⠈⠛⣰⠿⣆⠛⠁⠀⡀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⢀⣼⣿⣦⠀⠘⠛⠋⠀⣴⣿⠁⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⠀⠀⠀⣀⣤⣶⣾⣿⣿⣿⣿⡇⠀⠀⠀⢸⣿⣏⠀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠀⠀⣠⣶⣿⣿⣿⣿⣿⣿⣿⣿⠿⠿⠀⠀⠀⠾⢿⣿⠀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⣠⣿⣿⣿⣿⣿⣿⡿⠟⠋⣁⣠⣤⣤⡶⠶⠶⣤⣄⠈⠀⠀⠀⠀⠀⠀
⠀⠀⠀⢰⣿⣿⣮⣉⣉⣉⣤⣴⣶⣿⣿⣋⡥⠄⠀⠀⠀⠀⠉⢻⣄⠀⠀⠀⠀⠀
⠀⠀⠀⠸⣿⣿⣿⣿⣿⣿⣿⣿⣿⣟⣋⣁⣤⣀⣀⣤⣤⣤⣤⣄⣿⡄⠀⠀⠀⠀
⠀⠀⠀⠀⠙⠿⣿⣿⣿⣿⣿⣿⣿⡿⠿⠛⠋⠉⠁⠀⠀⠀⠀⠈⠛⠃
⠀⠀⠀⠀⠀⠀⠀⠉⠉⠉⠉⠉⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
                </div>
                </div>

            <?php
        }
    ?>

</body>
<?php
    include "_partials/_footer.php";
?>
</html>
