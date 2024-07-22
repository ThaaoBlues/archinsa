<?php

session_start();

?>

<!DOCTYPE html>
<html lang="fr">
<?php
    $titre_page = "Déconnection d'Arch'INSA";
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
    <h2>Merci d'être passé sur Arch'INSA ! ~\_(^-^)_/~</h2>

</body>
<?php
    echo $csrf->script($context='deconnection', $name='jeton_csrf', $declaration='var', $time2Live=-1, $max_hashes=5);
    include "_partials/_footer.php";
?>
</html>
