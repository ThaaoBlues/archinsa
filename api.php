<?php
    session_start();

    /*
        status :
        1 => Requète valide
        0 => Erreur pendant le traitement de la requète
        2 => Requète invalide
        3 => Session expirée 
        4 => Utilisateur non authentifié,  requète interdite

    */

    include("session_verif.php");
    include("test_creds.php");
    include("bdd.php");


    // Get the requested URL
    $request_uri = $_SERVER['REQUEST_URI'];

    // Split the URL into an array using the '/' delimiter
    $url_parts = explode('/', $request_uri);

    // Remove empty elements from the array
    $url_parts = array_filter($url_parts);

    // The first element is the base path (in this case, "/api")
    $base_path = array_shift($url_parts);
    

    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(isset($_GET["auth"])){
            try{
                $_SESSION["utilisateur_authentifie"] = true;
                session_regenerate_id(true);
                $_SESSION["heure_debut"] = time();
                echo json_encode(["status"=>"1","msg"=>"Authentification réussie."]);
            }catch(Exception $e){
                echo( json_encode(["status"=> "0","msg"=> $e->getMessage() ]) );
            }
    
        }
    
        if(isset($_GET["unauth"])){
            $_SESSION["utilisateur_authentifie"] = false;
            echo json_encode(["status"=>"1","msg"=>"Déconnection réussie."]);
            session_destroy();
            session_abort();
        }
    
        if(isset($_GET["test_auth"])){
            if($_SESSION["utilisateur_authentifie"] == true){
                echo(json_encode(["status"=> "1","msg"=> "Utilisateur bien authentifié."]));
            }else{
                echo(json_encode(["status"=> "4","msg"=> "Utilisateur non authentifié."]));
            }
        }
    
    }



    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        switch(array_shift($url_parts)){
            case "aj_doc":
                ajouter_doc($_POST);
                break;
            default:
                echo(json_encode(["status"=> "2","msg"=> "Opération inconnue."]));
        }
        
    }
?>