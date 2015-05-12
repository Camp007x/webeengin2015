<?php
error_reporting(E_ALL);
#error_reporting(0);
include 'classes.php';
//include 'database.php';

class UserController {
	
	public function doIt() {
		$data = RestUtils::processRequest(); 

		switch ($data->getMethod()) {  
			case 'get':  
				$this->getUser($data->getData());
				break; 
			case 'post':  
				$this->regUser($data->getData());
				break;  
			case 'delete':
				$this->deleteUser($data);
			break;
			case 'update':
				$this->changeUser($data);
			break;
			default:
				RestUtils::sendResponse(501,'text');  
			break;
		}  
	}
	
	private function getUser($data) {
		$content = $data->getData();
		$identity = $content->identity;
		
		$mysqli = mysqli_connect('localhost', 'wa5158_6', 'WSSers423+dad', 'wa5158_db6');
		$result = mysqli_query($mysqli, 'SELECT identity, salt_masterkey, pubkey_user, privkey_user_enc FROM user where identity like "' . $identity . '"');

		if ( !$result ) {
			RestUtils::sendResponse(500, "No Connection to Database possible!"); //erfolgreich getestet
		} else {
			if (mysqli_num_rows ( $result ) == 1) {
				$row = mysqli_fetch_assoc($result);
				
				$returnUser = new User();
				
				$returnUser->identity = $row['identity'];
				$returnUser->salt_masterkey = $row['salt_masterkey'];
				$returnUser->pubkey_user = $row['pubkey_user'];
				$returnUser->privkey_user_enc = $row['privkey_user_enc'];
				
				//Speicher freigeben
				mysqli_free_result($result);
				
				RestUtils::sendResponse(200, $returnUser); //erfolgreich getestet
			} else {
				RestUtils::sendResponse(404, "User not found!");  //erfolgreich getestet
			}
		}
	}
	
	private function regUser($data) {

		
		$content = $data;
		$identity = $content->identity;
		$salt_masterkey = $content->salt_masterkey;
		$pubkey_user = $content->pubkey_user;
		$privkey_user_enc = $content->privkey_user_enc;
		
		$mysqli = mysqli_connect('localhost', 'wa5158_6', 'WSSers423+dad', 'wa5158_db6');
		
		$sql = "INSERT INTO user (identity, salt_masterkey, pubkey_user, privkey_user_enc) VALUES ('" .
							$identity . "', '" . $salt_masterkey. "', '" . $pubkey_user . "', '" . 
									$privkey_user_enc . "')";
				
		$result = mysqli_query( $mysqli, $sql );
		


		if($result) {  
		//gib gespeichertes Element zurück.
			$result = mysqli_query($mysqli, 'SELECT identity, salt_masterkey, pubkey_user, privkey_user_enc FROM user where identity like "' . $identity . '"');
			if (mysqli_num_rows ( $result ) == 1) {
					$row = mysqli_fetch_assoc($result);

					$returnUser = new User();

					$returnUser->identity = $row['identity'];
					$returnUser->salt_masterkey = $row['salt_masterkey'];
					$returnUser->pubkey_user = $row['pubkey_user'];
					$returnUser->privkey_user_enc = $row['privkey_user_enc'];

					//Speicher freigeben
					mysqli_free_result($result);

					RestUtils::sendResponse(200, $returnUser);    //erfolgreich getestet
			}
		}			
		else {
			RestUtils::sendResponse(409, "User exists!");   //erfolgreich getestet
		}  
	}

//todo: implementieren
	private function deleteUser() {

		$data = RestUtils::processRequest(); 
		RestUtils::sendResponse(404, "User not found!"); 
		

}
//todo: implementieren
	private function changeUser() {

		$data = RestUtils::processRequest(); 
		RestUtils::sendResponse(404, "User not found!"); 
		
}

}

class PubKeyController {
	
	public function doIt() {
		$data = RestUtils::processRequest(); 
		
		switch ($data->getMethod()) {  
			case 'get':  
				$this->getUser($data);
				break; 
			default:
				RestUtils::sendResponse(501);  
			break;
		}  
	}
	
		
	private function getPubKeyOfUser($data) {
		$content = $data->getData();
		$identity = $content->identity;
		
		$mysqli = mysqli_connect('localhost', 'wa5158_6', 'WSSers423+dad', 'wa5158_db6');
		$result = mysqli_query($mysqli, 'SELECT identity, pubkey_user FROM user where identity like "' . $identity . '"');

  
		//gib gespeichertes Element zurück.
		if (mysqli_num_rows ( $result ) == 1) {
				$row = mysqli_fetch_assoc($result);

				$returnUser->identity = $row['identity'];
				$returnUser->pubkey_user = $row['pubkey_user'];

				//Speicher freigeben
				mysqli_free_result($result);

				RestUtils::sendResponse(200, $returnUser);   //erfolgreich getestet
		}
		else {
				RestUtils::sendResponse(404, "User not found!"); //erfolgreich getestet
		} 
	}
}

class AllUsersController {
	
	public function doIt() {
		$data = RestUtils::processRequest(); 
		
		switch ($data->getMethod()) {  
			case 'get':  
				$this->getAllUsers();
				break; 
			default:
				RestUtils::sendResponse(501);  
			break;
		}  
	}
	
	private function getAllUsers() {
		$output = array();
	
		//todo allgemeine Datenbankklasse erstellen
		$mysqli = mysqli_connect('localhost', 'wa5158_6', 'WSSers423+dad', 'wa5158_db6');
		$result = mysqli_query($mysqli, 'SELECT identity FROM user order by number');
		
		//Array zusammenbauen aus dem Ergebnis der Abfrage
		while ($row = mysqli_fetch_assoc($result)) {
		   $output['users'][] = $row['identity'];
		}
		
		//Speicher freigeben
		mysqli_free_result($result);
		
		//Antwort geht raus
		RestUtils::sendResponse(200, $output);  //erfolgreich getestet

	}
}


class MessageController {
	
	public function doIt() {
		$data = RestUtils::processRequest(); 
		
		switch ($data->getMethod()) {  
			case 'get':  
				$this->getMessages($data);
				break; 
			case 'post':  
				$this->sendMessage($data);
				break;  
			default:
				RestUtils::sendResponse(501);  
			break;
		}  
	}
	
	private function getMessages($data) {
		$request = $data->getData();
			
		$message = "Kohl mit Hack!";
			
		RestUtils::sendResponse(200, $message);
	}
	
	private function sendMessage($data) {
		//TODO store Message
				
		RestUtils::sendResponse(200, json_encode($data->getData()));  
		break;  
	}
	
}

?>