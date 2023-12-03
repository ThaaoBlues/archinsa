<?php
session_start();

function verifier_session(){
    if(isset($_SESSION["utilisateur_authentifie"])){
        // vérifie que la session ne dépasse pas 4h
        if((time() - $_SESSION["heure_debut"]) > 3600*4){
            session_destroy();
            session_abort();
            echo(json_encode(array("status"=> "3","msg"=>"Session expirée, veuillez vous reconnecter.")));
        }
    }else{
        echo(json_encode(array("status"=> "0","msg"=> "Utilisateur non connecté.")));
        exit;
    }
}


?>