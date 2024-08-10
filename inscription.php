<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<?php
    $titre_page = "Inscription sur Arch'INSA";
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
        
        <h4 class=" centre-txt label-input" for="insa-input">Selectionne ton INSA</h4>
        <select class="champ" id="insa-input" type="select" name="insa" required>

            <option value="insa_toulouse">INSA Toulouse &lt;3</option>
            <option value="insa_lyon">INSA Lyon</option>
            <option value="insa_rennes">INSA Rennes</option>
            <option value="insa_cvl">INSA CVL</option>
            <option value="insa_hdf">INSA HDF</option>
            <option value="insa_rouen">INSA Rouen</option>
            <option value="insa_strasbourg">INSA Strasbourg</option>
            <option value="insa_hdf">INSA HDF</option>

        </select>
        
        <button class="submit-button color-red-tr" onclick="inscription()">S'inscrire !</button>
    </div>
    <h2>Oui c'est vide oui ~\_(^-^)_/~</h2>

</body>
<?php
    echo $csrf->script($context='inscription', $name='jeton_csrf', $declaration='var', $time2Live=-1, $max_hashes=5);
    include "_partials/_footer.php";
?>
</html>
