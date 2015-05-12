<?php 
class User {
    public $identity;  
	public $salt_masterkey; 
	public $masterkey;
	public $pubkey_user;
	public $privkey_user_enc;
	public $iv;
	private $password;
	private $privkey_user;
		

	public function __construct($input = '') {  
		$this->CalculateMasterKey();
	}  
	
	function setIdentity($name) {
		$this->identity = $name;
	}	
	
	function getIdentity() {
		return $this->identity;
	}
		
	function setPassword($password) {
		$this->password = $password;
	}
	
	function getPassword() {
		return $this->password;
	}

	
	function printUser() {
		echo "identity: " . $this->identity . '<br>';
		echo "passwort: " . $this->password . '<br>';
		echo "salt_masterkey: " .$this->salt_masterkey . '<br>';
		echo "masterkey: " .$this->masterkey . '<br>';
		echo "iv: " . $this->iv . '<br>';
		echo "pubkey_user: " . $this->pubkey_user . '<br>';
		echo "privkey_user: " . $this->privkey_user . '<br>';
		echo "privkey_user_enc: " . $this->privkey_user_enc . '<br>';
	}
	
	function generateSaltMasterkey() {
		//salt_masterkey erstellen
		
		$number1 = rand(5, 5452338454186);
		$number2 = rand(5, 9848382374832);
		$number3 = rand(5, 3847472833);
		
		$temp = $number1 * $number2 * $number3;
		
		$result = sha1($temp);

		//nutze erste 32 zeichen, da das verschlüsselungsverfahren bei mehr spackt
		$short_master_key = pack('A*', $result);
		$this->salt_masterkey = substr($short_master_key, -32); 
	}
	
	function calculateMasterKey() {
				
		$iterations = 1000;
		$salt = mcrypt_create_iv(16, MCRYPT_DEV_URANDOM);

		//http://php.net/manual/de/function.hash-pbkdf2.php
		// $this->masterkey = hash_pbkdf2("sha256", $this->getPassword(), $this->salt_masterkey, $iterations, 32);
	}
	
	function generateKeys() {
		$config = array(
			"digest_alg" => "sha512",
			"private_key_bits" => 4096,
			"private_key_type" => OPENSSL_KEYTYPE_RSA,
		);
		
		// Create the private and public key
		$res = openssl_pkey_new($config);

		// Extract the private key from $res to $privKey
		openssl_pkey_export($res, $privKey);
		$this->privkey_user = $privKey;
		
		// Extract the public key from $res to $pubKey
		$pubKey = openssl_pkey_get_details($res);
		$pubKey = $pubKey["key"];
		$this->pubkey_user = $pubKey;
		
		$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB); 
		$this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND); 
	}
	
	function encryptPrivKeyUser() {
		$encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->masterkey, $this->privkey_user , MCRYPT_MODE_ECB, $this->iv); 
		
		//speicherung des verschlüsselten keys
		$this->privkey_user_enc = $encrypted;
	}
	
	function decyptedPrivKeyUser() {
			CalculateMasterKey();
			
			//Entschlüsseln
			$result = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->salt_masterkey, $this->privkey_user_enc, MCRYPT_MODE_ECB, $this->iv);

			$this->privkey_user = rtrim($result, "\0\4");     
	}
}

class InnerMessage {
	public $identity;
	public $cipher;
	public $iv;
	public $key_recipient_enc;
	public $sig_recipient;
}

class Message {
	public $innerMessage;
	public $timestamp;
	public $recipient;
	public $sig_service;
}

class MessageRequest {
	public $identity;
	public $timestamp;
	public $signature;
}

class RestRequest  
{  
    private $request_vars;  
    private $data;   
    private $method;  
  
    public function __construct() {  
        $this->request_vars      = array();  
        $this->data              = '';  
        $this->method            = 'get';  
    }  
  
    public function setData($data)  {  
        $this->data = $data;  
    }  
  
    public function setMethod($method) {  
        $this->method = $method;  
    }  
  
    public function setRequestVars($request_vars) {  
        $this->request_vars = $request_vars;  
    }  
  
    public function getData() {  
        return $this->data;  
    }  
  
    public function getMethod() {  
        return $this->method;  
    }  
  
    public function getRequestVars() {  
        return $this->request_vars;  
    }  
}  

class RestUtils  {  
    public static function processRequest() {  
		$request_method = strtolower($_SERVER['REQUEST_METHOD']);  
		$return_obj     = new RestRequest();  
		$data           = array();  
	  
		switch ($request_method) {  
			case 'get':  
				$data = $_GET;  
				break; 
			case 'post':  
				$data = $_POST;  
				break;
			case 'delete':
				$data = file_get_contents("php://input");
				break;
			case 'update':
				$data = file_get_contents("php://input");
				break;
			// accept nothing else  
		}  
	  
		$return_obj->setMethod($request_method);  
	  
		$return_obj->setRequestVars($data);  
	  
		if(isset($data['data']))  
		{  
			$return_obj->setData(json_decode($data['data'], true));  
		}  else {
			$return_obj->setData(json_decode($data, true));  
		}
		return $return_obj;  
    }  
  
    public static function sendResponse($status = 200, $body) {  
	    $status_header = 'HTTP/1.1 ' . $status . ' ' . RestUtils::getStatusCodeMessage($status);  
		
		header($status_header);  
		header('Content-type: application/json');  
	  
		echo json_encode($body);  
		//exit;  
    }  
  
    public static function getStatusCodeMessage($status) {  
        // these could be stored in a .ini file and loaded  
        // via parse_ini_file()... however, this will suffice  
        // for an example  
        $codes = Array(  
            100 => 'Continue',  
            101 => 'Switching Protocols',  
            200 => 'OK',  
            201 => 'Created',  
            202 => 'Accepted',  
            203 => 'Non-Authoritative Information',  
            204 => 'No Content',  
            205 => 'Reset Content',  
            206 => 'Partial Content',  
            300 => 'Multiple Choices',  
            301 => 'Moved Permanently',  
            302 => 'Found',  
            303 => 'See Other',  
            304 => 'Not Modified',  
            305 => 'Use Proxy',  
            306 => '(Unused)',  
            307 => 'Temporary Redirect',  
            400 => 'Bad Request',  
            401 => 'Unauthorized',  
            402 => 'Payment Required',  
            403 => 'Forbidden',  
            404 => 'Not Found',  
            405 => 'Method Not Allowed',  
            406 => 'Not Acceptable',  
            407 => 'Proxy Authentication Required',  
            408 => 'Request Timeout',  
            409 => 'Conflict',  
            410 => 'Gone',  
            411 => 'Length Required',  
            412 => 'Precondition Failed',  
            413 => 'Request Entity Too Large',  
            414 => 'Request-URI Too Long',  
            415 => 'Unsupported Media Type',  
            416 => 'Requested Range Not Satisfiable',  
            417 => 'Expectation Failed',  
            500 => 'Internal Server Error',  
            501 => 'Not Implemented',  
            502 => 'Bad Gateway',  
            503 => 'Service Unavailable',  
            504 => 'Gateway Timeout',  
            505 => 'HTTP Version Not Supported'  
        );  
  
        return (isset($codes[$status])) ? $codes[$status] : '';  
    }  
}  
  
?>