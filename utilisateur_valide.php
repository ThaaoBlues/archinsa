<?php

session_start();

?>

<!DOCTYPE html>
<html lang="fr">
<?php
    $titre_page = "Validation de votre compte Arch'INSA";
    include "_partials/_head.php";
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
    <h1>Votre compte a bien été validé !!</h1>
    <a hre="connection.php">Se connecter à Arch'INSA</a>

</body>
<?php
    include "_partials/_footer.php";
?>
</html>
