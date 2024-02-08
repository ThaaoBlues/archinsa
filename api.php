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

    //include("session_verif.php");
    include("bdd.php");

    include('php-csrf.php');
    $csrf = new CSRF();


    // Get the requested URL
    $request_uri = $_SERVER['REQUEST_URI'];

    // Split the URL into an array using the '/' delimiter
    $url_parts = explode('/', $request_uri);

    // Remove empty elements from the array
    $url_parts = array_filter($url_parts);

    // The first element is the base path (in this case, "/api")
    $base_path = array_shift($url_parts);
    

    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        // enlève les variables de requète
        $endpoint = explode("?",array_pop($url_parts))[0];
        
        switch($endpoint){
            case 'auth':
                try{
                    $_SESSION["utilisateur_authentifie"] = true;
                    session_regenerate_id(true);
                    $_SESSION["heure_debut"] = time();
                    echo(json_encode(["status"=>"1","msg"=>"Authentification réussie."]));
                }catch(Exception $e){
                    echo( json_encode(["status"=> "0","msg"=> $e->getMessage() ]) );
                }
                break;

            case 'unauth':
                $_SESSION["utilisateur_authentifie"] = false;
                echo json_encode(["status"=>"1","msg"=>"Déconnection réussie."]);
                session_destroy();
                session_abort();
                break;

            case 'test_auth':
                if($_SESSION["utilisateur_authentifie"] == true){
                    echo(json_encode(["status"=> "1","msg"=> "Utilisateur bien authentifié."]));
                }else{
                    echo(json_encode(["status"=> "4","msg"=> "Utilisateur non authentifié."]));
                }
                break;


            case 'rechercher':

                // Exemple URL: /api.php/chercher?req=math&duree=30&themes=algebre,geometrie
                
                $query = isset($_GET["req"]) ? $_GET["req"] : "";
                $length = isset($_GET["duree"]) ? $_GET["duree"] : "";
                $themes = isset($_GET["themes"]) ? explode(",", $_GET["themes"]) : [];
                //print_r($_GET);
                try {
                    $results = RechercheExercices($query, $length, $themes);
                    echo json_encode(["status" => "1", "resultats" => $results]);
                } catch (Exception $e) {
                    echo json_encode(["status" => "0", "msg" => $e->getMessage()]);
                }
                
        
                break;

            case 'decomposer_ensemble':

                // Récupération de l'ID de l'ensemble et du thème depuis l'URL ou autrement
                $ensembleId = isset($_GET['ensemble_id']) ? intval($_GET['ensemble_id']) : '';

                // Vérification de la validité de l'ID de l'ensemble
                if (!empty($ensembleId)) {
                    // Préparation de la requête SQL pour obtenir les informations sur l'ensemble
                    $sqlEnsemble = 'SELECT * FROM ensembles WHERE id = ?';
                    $stmtEnsemble = $conn->prepare($sqlEnsemble);
                    $stmtEnsemble->bind_param('i', $ensembleId);
                    $stmtEnsemble->execute();
                    $resultEnsemble = $stmtEnsemble->get_result();
                    $ensemble = $resultEnsemble->fetch_assoc();

                    if ($ensemble && $ensemble['valide'] == true) {
                    
                        // Préparation de la requête SQL pour obtenir les informations sur les exercices sélectionnés
                        $sqlDocu = "SELECT * FROM documents WHERE ensemble_id=?";
                        $stmtDocu = $conn->prepare($sqlDocu);
                        $stmtDocu->bind_param('i', $ensembleId);
                        $stmtDocu->execute();
                        $resultDocu = $stmtDocu->get_result();

                        $ensemble["documents"] = array();

                        while ($doc = $resultDocu->fetch_assoc()) {

                            switch ($doc['type']) {
                                case 1:

                                    // on va maintenant prendre chaque exercice un par un
                                    // et afficher les bonnes infos :

                                    $sqlExos = "SELECT * FROM exercices WHERE document_id=?";
                                    $stmtExos = $conn->prepare($sqlExos);

                                    $stmtExos->bind_param('i', $doc["id"]);
                                    $stmtExos->execute();
                                    $resultExos = $stmtExos->get_result();
                                    $doc["exercices"] = array();

                                    while ($exo = $resultExos->fetch_assoc()) {
                                        array_push($doc["exercices"],$exo);
                                    }

                                    array_push($ensemble["documents"],$doc);

                                    

                                break;
                            }

                        }

                        echo(json_encode(["status"=>"1","msg"=>$ensemble]));


                    }else{
                        echo(json_encode(['status'=> '2','msg'=> "Vous devez spécifier un indetifiant d'ensemble valide dans votre requête."]));

                    }

                }else{
                    echo(json_encode(['status'=> '2','msg'=> "Vous devez spécifier un indetifiant d'ensemble dans votre requête."]));
                }

                break;

            case "generer_chronologie":

                try{

                    $res = generer_chronologie();
    
                    echo(json_encode(["status"=>"1","resultats"=>$res]));
                    
                }catch(Exception $e){
                    echo( json_encode(["status"=> "0","msg"=> $e->getMessage() ]) );
                }
               
                break;

            default:
                echo(json_encode(['status'=> '2','msg'=> "Ce point d'arrivée n'existe pas dans l'api."]));
                break;




        }

    
    }



    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        verifier_session();

        switch(array_pop($url_parts)){
            case "aj_doc":


                if(!$csrf->validate($context='televersement',$_POST["jeton-csrf"])){
                    echo( json_encode(["status"=> "2","msg"=>"jeton csrf manquant.".$_POST["jeton-csrf"]]) );
                    break;
                }

                try{
                    ajouter_doc($_POST);

                }catch(Exception $e){
                    echo( json_encode(["status"=> "0","msg"=> $e->getMessage() ]) );
                }
                break;

            case "valider_ensemble":

                if(!$csrf->validate($context='valider_ensemble',$_POST["jeton-csrf"])){
                    echo( json_encode(["status"=> "2","msg"=>"jeton csrf manquant.".$_POST["jeton-csrf"]]) );
                    break;
                }
                try{
                    valider_ensemble($_POST["ensemble_id"]);
                    echo(json_encode(["status"=>"1","msg"=>"Ensemble validé."]));
                }catch(Exception $e){
                    echo( json_encode(["status"=> "0","msg"=> $e->getMessage() ]) );
                }
                break;

            case "supprimer_ensemble":

                if(!$csrf->validate($context='supprimer_ensemble',$_POST["jeton-csrf"])){
                    echo( json_encode(["status"=> "2","msg"=>"jeton csrf manquant." ]) );
                    break;
                }

                try{
                    supprimer_ensemble($_POST["ensemble_id"]);
                    echo(json_encode(["status"=>"1","msg"=>"Ensemble supprimé."]));
                }catch(Exception $e){
                    echo( json_encode(["status"=> "0","msg"=> $e->getMessage() ]) );
                }
                break;
            default:
                echo(json_encode(["status"=> "2","msg"=> "Opération inconnue."]));
        }

        exit;
        
    }
?>