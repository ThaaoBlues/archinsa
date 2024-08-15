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

    include("bdd.php");

    include('php-csrf.php');
    include_once("utils/sendmail.php");
    include_once("utils/token.php");
    include_once("utils/inputs.php");

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


            case 'rechercher':

                // Exemple URL: /api.php/chercher?req=math&duree=30&themes=algebre,geometrie
                
                $query = isset($_GET["req"]) ? $_GET["req"] : "";
                $length = isset($_GET["duree"]) ? $_GET["duree"] : "";
                $themes = isset($_GET["themes"]) ? explode(",", $_GET["themes"]) : [];
                $tout_les_insa = isset($_GET["tout_les_insa"]) ? true : false;
                //print_r($_GET);
                try {
                    $results = RechercheExercices($query, $length, $themes,$tout_les_insa);
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


            
            case "verification_inscription":

                $succes = isset($_GET["token"]);

                if(!$succes){
                    return $succes;
                }


                $token = htmlspecialchars($_GET["token"]);

                $succes = verifier_utilisateur($token);
                if($succes){
                    header("Location: /utilisateur_valide.php");
                    //echo( json_encode(["status"=> 1,"msg"=> "Utilisateur verifié !" ]) );
                }else{
                    echo( json_encode(["status"=> "0","msg"=> "Une erreur est survenue lors de votre vérification ou vous avez essayé de modifier le contenu de la requête :/" ]) );
                }
                break;

            default:
                echo(json_encode(['status'=> '2','msg'=> "Ce point d'arrivée n'existe pas dans l'api."]));
                break;




        }

    
    }



    if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
        $user_auth = isset($_SESSION["utilisateur_authentifie"]) && ($_SESSION["utilisateur_authentifie"] == 1);
        $admin_auth = $user_auth && isset($_SESSION["admin"]) && ($_SESSION["admin"] == 1);
        switch(array_pop($url_parts)){
            case "aj_doc":
                if($user_auth){

                    /*if(!$csrf->validate($context='televersement',$_POST["jeton-csrf"])){
                        echo( json_encode(["status"=> "2","msg"=>"jeton csrf manquant ou invalide. ( contenu du champ : ".$_POST["jeton-csrf"]." )"]) );
                        break;
                    }*/

                    try{
                        ajouter_doc($_POST);

                    }catch(Exception $e){
                        echo( json_encode(["status"=> "0","msg"=> $e->getMessage() ]) );
                    }
                    break;
                }else{
                    break;
                }

            case "valider_ensemble":

                if($admin_auth){
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
                }
                
                break;

            case "supprimer_ensemble":

                if($admin_auth){
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
                }
                
                break;

            case "connection":

                if(!$csrf->validate($context='connection',$_POST["jeton-csrf"])){
                    echo( json_encode(["status"=> "2","msg"=>"jeton csrf manquant." ]) );
                    break;
                }

                $username = $_POST['username'];
                $password = $_POST['password'];

                $succes = connecter_utilisateur(htmlspecialchars($username),$password);
                
                
                if($succes){
                    echo( json_encode(["status"=> "1","msg"=> "Utilisateur connecté !" ]) );
                }else{
                    echo( json_encode(["status"=> "0","msg"=> "Utilisateur inconnu, non vérifié par mel ou informations d'identification erronées." ]) );
                }
                break;


            case "deconnection":
                if(!$csrf->validate($context='deconnection',$_POST["jeton-csrf"])){
                    echo( json_encode(["status"=> "2","msg"=>"jeton csrf manquant." ]) );
                    break;
                }
                session_destroy();
                echo( json_encode(["status"=> "1","msg"=> "Utilisateur déconnecté !" ]) );
                break;

            case "inscription":


                

                if(!$csrf->validate($context='inscription',$_POST["jeton-csrf"])){
                    echo( json_encode(["status"=> "2","msg"=>"jeton csrf manquant." ]) );
                    break;
                }

                $username = $_POST['username'];
                $password = $_POST['password'];
                $nom_insa = $_POST['nom_insa'];
                
                $username = assainir_et_valider_mel($username);

                if($username == "[ERREUR_MEL_MALSAINT]"){
                    echo(json_encode(["status"=> "2","msg"=> "Votre adresse mel n'a pas passé les filtres de sécurité :/ ( MOUAHAHAHAHA )" ]));
                    break;
                }

                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                $token = inscription_utilisateur(htmlspecialchars($username),$password_hash,$nom_insa);
                $succes = $token != "[ERREUR]";
                if($succes){
                    $mailtest = new Mail();
                    $mailtest->setContent(
                        "Inscription sur Arch'INSA",
                        "https://127.0.0.1/archinsa/api.php/verification_inscription?token=".$token,
                        "Salut Salut !!",
                        "La validation du compte permettra de vous connecter et de publier du contenu sur Arch'INSA :D",
                    );
                    if(!$mailtest->send("mougnibas@insa-toulouse.fr", "Eh toi là !")) {
                        echo $mailtest->getError(); //si le mail n'a pas été envoyé
                        $succes = false;
                    }
                    
                }
                if($succes){
                    echo( json_encode(["status"=> 1,"msg"=> "Pour finaliser l'inscription et pouvoir vous connecter, veuillez valider votre compte via le mel que nous vous avons envoyé :)" ]) );
                }else{
                    echo( json_encode(["status"=> 0,"msg"=> "Une erreur est survenue lors de votre inscription ou vous avez essayé de modifier le contenu de la requête :/" ]) );
                }
                
                break;

            default:
                echo(json_encode(["status"=> "2","msg"=> "Opération inconnue."]));
        }

        exit;
        
    }

