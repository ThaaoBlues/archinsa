<?php

session_start();

?>

<!DOCTYPE html>
<html lang="fr">
<?php
    $titre_page = "Connection sur Arch'INSA";
    include "_partials/_head.php";
    include('php-csrf.php');
    $csrf = new CSRF();

?>
<body>

<div class="centre-horizontal bulle-rouge" id="titre">
    <pre class="centre-txt gros-titre">
   __    ____   ___  _   _ /'/ ____  _  _  ___    __   
  /__\  (  _ \ / __)( )_( )   (_  _)( \( )/ __)  /__\  
 /(__)\  )   /( (__  ) _ (     _)(_  )  ( \__ \ /(__)\ 
(__)(__)(_)\_) \___)(_) (_)   (____)(_)\_)(___/(__)(__)
    </pre>

</div>
    <div class="formulaire">
        <input class="champ" id="username-input" type="text" name="username" placeholder="Nom d'utilisateur" required>
        <input class="champ" id="password-input" type="password" name="password" placeholder="Mot de passe" required>
        <button class="submit-button color-red-tr" onclick="connection()">Se connecter</button>
    </div>
    <h2>Oui c'est vide oui ~\_(^-^)_/~</h2>

</body>
<?php
    echo $csrf->script($context='connection', $name='jeton_csrf', $declaration='var', $time2Live=-1, $max_hashes=5);
    include "_partials/_footer.php";
?>
</html>
