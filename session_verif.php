<?php
session_start();

// Load the CAS lib
require_once("phpCAS-1.6.1/CAS.php");

// Enable debugging
phpCAS::setLogger();
// Enable verbose error messages. Disable in production!
phpCAS::setVerbose(true);

// Initialize phpCAS
phpCAS::client(CAS_VERSION_2_0, "cas.insa-toulouse.fr", 443, 'cas',"https://annales.insat.fr");

// For production use set the CA certificate that is the issuer of the cert
// on the CAS server and uncomment the line below
// phpCAS::setCasServerCACert($cas_server_ca_cert_path);

// For quick testing you can disable SSL validation of the CAS server.
// THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
// VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
//phpCAS::setNoCasServerValidation();

// force CAS authentication
phpCAS::forceAuthentication();

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().

// logout if desired
if (isset($_REQUEST['logout'])) {
        phpCAS::logout();
        $_SESSION["utilisateur_authentifie"] = false;
}

$_SESSION["utilisateur_authentifie"] = true;

function verifier_session(){

    return json_encode(["status"=>1,"msg"=>"Bonjour ".phpCAS::getUser()." !"]);
    //return json_encode(["status"=>1,"msg"=>"Bonjour !"]);

}
$ADMINS = array("mougnibas","rebillar");

function admin_seulement(){
    global $ADMINS;
    if(!in_array(phpCAS::getUser(), $ADMINS)) {
        header("Location: /index.php");
    }
}


?>