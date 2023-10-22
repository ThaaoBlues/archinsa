<?php
session_start();

if(isset($_SESSION["utilisateur_authentifie"])){
    // vérifie que la session ne dépasse pas 4h
    if((time() - $_SESSION["heure_debut"]) > 3600*4){
        session_destroy();
        session_abort();
        echo(json_encode(array("status"=> "3","msg"=>"Session expirée, veuillez vous reconnecter.")));
    }
}

?>