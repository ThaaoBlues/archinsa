<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "phpmailer/Exception.php";
require_once "phpmailer/PHPMailer.php";
require_once "phpmailer/SMTP.php";
include("test_creds.php");

class Mail
{
    private static $mail = NULL;
    private static $error = "";

    private function readFile($file)
    {
        $real_path = $file;
        $file = fopen($real_path, "r") or die("Unable to open file!");;
        $password = fgets($file);
        fclose($file);
        return trim($password);
    }

    public function __construct()
    {
        global $mel_id,$mel_adr,$mel_mdp;
        try {
            $this::$mail = new PHPMailer(true);
            $this::$mail->isSMTP();
            $this::$mail->Host = "192.168.200.9";
            $this::$mail->SMTPAuth = true;
            $this::$mail->Username = $mel_id;
            $this::$mail->Password = $mel_mdp;
            $this::$mail->setFrom($mel_adr, name: 'Club Info INSA Toulouse');
            $this::$mail->isHTML(true);
            $this::$mail->Subject = 'Inscription sur Arch\'INSA';
            $this::$mail->Body = 'Message vide.';
            $this::$mail->CharSet = 'UTF-8';
        } catch (Exception $e) {
            null;
        }
    }

    public function setContent(string $subject,string $url,string $titre,string $paragraphe)
    {
        try {
            //sécu et encodage en UTF-8 (n'échappe pas les ')
            $subject = mb_convert_encoding($subject, 'UTF-8', 'auto');
            $this::$mail->Subject = htmlspecialchars($subject, ENT_NOQUOTES, 'UTF-8');

            $template = file_get_contents("utils/phpmailer/template_mel.html");
            $content = str_replace("[url_token]", $url, $template);
            $content = str_replace("[titre]", $titre, $content);
            $content = str_replace("[paragraphe]", $paragraphe, $content);


            $this::$mail->Body = $content;
        } catch (Exception $e) {
            null;
        }
    }

    public function send(string $mail_dest, string $name_dest): bool
    {
        try {
            $mail_dest=htmlspecialchars($mail_dest);
            $name_dest=htmlspecialchars($name_dest);
            $this::$mail->addAddress($mail_dest, $name_dest);
            $this::$mail->Port = 25;
            //$this::$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this::$mail->send();
        } catch (Exception $e) {
            $this::$error=$this::$mail->ErrorInfo;
            return false;
        }
        return true;
    }

    public function getError(): string
    {
        return $this::$error;
    }
}

/*
echo "test d'envoi de mail (sans token) ...";
$mailtest = new Mail();
$mailtest->setContent("sujet du mail", "titre du mail", "<p>ceci est un test</p><p>ceci est une seconde ligne</p>");
if(!$mailtest->send("mougnibas@insa-toulouse.fr", "test")) {
    echo $mailtest->getError(); //si le mail n'a pas été envoyé
} else {
    echo "coul coul coul"; // si le mail a été envoyé
}
*/

?>
