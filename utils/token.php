<?php
// Database connection parameters
include("test_creds.php");

class Token
{
	private static $conn; 
	public function __construct()
	{	
		global $servername,$db_username,$db_password,$dbname;
		self::$conn = new mysqli($servername, $db_username, $db_password, $dbname);
		// Check connection
		if (self::$conn->connect_error) {
			die("Connection failed: " . self::$conn->connect_error);
		}
	}

	private function randomStr($str_len) : string 
	{
		$random_str_tot = "";
	    $chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	    for( $x = 0; $x < $str_len; $x++ ) {
	        $random_str= $chars[random_int(0, strlen($chars)-1)];
	        $random_str_tot = $random_str_tot.$random_str;
	    }
	    return $random_str_tot;
	}

	public function delete(int $id_user, string $token)
	{
		$token = htmlspecialchars($token);
		$id_user = htmlspecialchars($id_user);
        $deleteReq = self::$conn->prepare("DELETE FROM `token` WHERE `id_user` = ? AND `token` = ?");
        $deleteReq->execute(array($id_user, $token));
	}

	public function isValid(int $id_user, string $token) : bool
	{
		$id_user = htmlspecialchars($id_user);
		$token = htmlspecialchars($token);
		$req = self::$conn->prepare("SELECT `TOKEN`, `create_time` FROM `token` WHERE `id_user` = ? AND `TOKEN` = ?");
    	$ret = $req->execute(array($id_user, $token));
		
		if($ret){
			$req->store_result();
			$req->bind_result($dbToken,$createTime);
			$req->fetch();
	        $createTime = strtotime($createTime);
	        $currentTime = time();


	        $oneDayInSeconds = 86400; // 1 jour = 86400 s
			
			$ret = $currentTime - $createTime <= $oneDayInSeconds;

			if(!$ret){
	            // OLD TOKEN (+ d'un jour donc expiré)
	            $deleteReq = self::$conn->prepare("DELETE FROM `token` WHERE `id_user` = ?");
	            $deleteReq->execute(array($id_user));
	        }
		}
		return $ret;
	}

	public function Add(int $id_user) : string
	{
		$id_user = (int) htmlspecialchars($id_user);
		$token = $this->randomStr(50);
		
		// supprimer les anciens token
		$deleteReq = self::$conn->prepare("DELETE FROM `token` WHERE `id_user` = ?");
		$deleteReq->execute(array($id_user));
	    
	    $req = self::$conn->prepare("INSERT INTO `token`(`id_user`, `TOKEN`, `create_time`) VALUES(?, ?, ?)");
		$req->execute(array($id_user, $token, date("Y-m-d H:i:s", time())));
		return $token;
	}

	public function getUserID(string $token) : int
	{
		$token = htmlspecialchars($token);
		$req = self::$conn->prepare("SELECT `id_user` FROM `token` WHERE `TOKEN` = ?");
    	$req->execute(array($token));

		$req->store_result();
		$req->bind_result($id_user);
		$res = $req->fetch();

    	if ($res) {
    		return $id_user;
    	} else {
    		return -1;
    	}
	}

	public function getToken(string $user_id) : string
	{
		$req = self::$conn->prepare("SELECT `TOKEN` FROM `token` WHERE `id_user` = ?");
    	$req->execute(array($user_id));

		$req->store_result();
		$req->bind_result($token);
		$res = $req->fetch();

    	if ($res) {
    		return $token;
    	} else {
    		return -1;
    	}
	}
}
?>