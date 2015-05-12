<?php 

error_reporting(E_ALL);
#error_reporting(0);
include '../server/classes.php';


###################################################
######## Tests-Steuerung
###################################################
define('TESTCASE_1',true);  //(User/regUser)
define('TESTCASE_2',false);	 //(User/getUser)
define('TESTCASE_3',false);  //(User/deleteUser)
define('TESTCASE_4',false);  //(User/changeUser)
define('TESTCASE_5',false);  //(User/getAllUsers)
define('TESTCASE_6',false);   //(User/getPubKeyOfUser)
define('TESTCASE_7',false);  //(Message/sendMessage)
define('TESTCASE_8',false);  //(Message/getMessage)

###################################################
######## Vorbereitung für alle Schnittstellentests
###################################################

$regUser = new User();
/*


*/
###################################################
######## //Testfall für Schnittstelle 1 (User/regUser)
###################################################
if(TESTCASE_1) {
	
	echo "<h3>Testfall für Schnittstelle 1 (User/regUser))</h3>";
	echo "Generiere Testdaten für Testuser...";
	
	
$regUser->identity = "ThomasMueller1";
$regUser->setPassword("igel");
$regUser->generateSaltMasterkey();
$regUser->CalculateMasterKey();
$regUser->generateKeys();
#$regUser->CompareKeys();
#$regUser->printUser();

	
	$regUser->printUser();
	
	$postdata = http_build_query(
		array(
			'data' => json_encode($regUser)
		)
	);

	$opts = array('http' =>
		array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata
		)
	);

	$context  = stream_context_create($opts);
	$result = file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/server/index.php/User/regUser", false, $context);

	print_r($result);
	
	
}
###################################################
######## //Testfall für Schnittstelle 2 (User/getUser)
###################################################
if(TESTCASE_2) {
$regUser->identity = "sandra";

echo "<h3>Testfall für Schnittstelle 2 (User/getUser)</h3>";
echo "Userdaten für " . $regUser->identity . " werden angefordert... " . '<br>';



#$regUser->generateTestKeys();

$postdata = http_build_query(
    array(
        'data' => json_encode($regUser)
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context  = stream_context_create($opts);
$result = file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/server/index.php/User/getUser", false, $context);

print_r($result);

$user = json_decode($result);

echo "Antwort: <br>";
echo $user->identity . '<br>';
echo $user->salt_masterkey. '<br>';
echo $user->pubkey_user. '<br>';
echo $user->privkey_user_enc. '<br>';

echo '--------Ende des Testfalls --------';
}
###################################################
######## //Testfall für Schnittstelle 3 (User/deleteUser)
###################################################
if(TESTCASE_3) {
$regUser->identity = "sandra";

echo "<h3>Testfall für Schnittstelle 3 (User/deleteUser)</h3>";
echo "User " . $regUser->identity . " soll gelöscht werden " . '<br>';


//todo: implementieren

echo '--------Ende des Testfalls --------';
}
###################################################
######## //Testfall für Schnittstelle 4 (User/changeUser)
###################################################-
if(TESTCASE_4) {
$regUser->identity = "sandra";

echo "<h3>Testfall für Schnittstelle 3 (User/changeUser)</h3>";
echo "User " . $regUser->identity . " soll geändert werden " . '<br>';


//todo: implementieren

echo '--------Ende des Testfalls --------';
}
###################################################
######## //Testfall für Schnittstelle 5 (User/getAllUsers)
###################################################
if(TESTCASE_5) {
	
echo "<h3>Testfall für Schnittstelle 5 (User/getAllUsers)</h3>";
echo "Alle Benutzernamen werden angefordert: " . '<br>';

$postdata = http_build_query(
    array(
        'data' => json_encode($regUser)
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context  = stream_context_create($opts);
$result = file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/server/index.php/User/getAllUsers", false, $context);

$resultobject = json_decode($result);
$users_as_array = $resultobject->users;

echo "Anzahl:" . count($users_as_array) . '<br>';
foreach($users_as_array as $item) {
	echo $item . '<br>';
}
echo '--------Ende des Testfalls --------';

}
###################################################
######## //Testfall für Schnittstelle 6 (User/getPubKeyOfUser)
###################################################
if(TESTCASE_6) {
$regUser->identity = "thomas";	
	
echo "<h3>Testfall für Schnittstelle 6 (User/getPubKeyOfUser)</h3>";
echo "Öffentlicher Schlüssel wird angefordert für: " . $regUser->identity  . '<br>';

$postdata = http_build_query(
    array(
        'data' => json_encode($regUser)
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context  = stream_context_create($opts);
$result = file_get_contents("http://" . $_SERVER["SERVER_NAME"] . "/server/index.php/User/getPubKeyOfUser", false, $context);

echo "Öffentlicher Schlüssel: <br>";
print_r($result);



echo '--------Ende des Testfalls --------';

}
###################################################
######## //Testfall für Schnittstelle 7 (Message/sendMessage)
###################################################
if(TESTCASE_7) {
$regUser->identity = "sandra";

echo "<h3>Testfall für Schnittstelle 3 (Message/sendMessage)</h3>";



//todo: implementieren

echo '--------Ende des Testfalls --------';
}
###################################################
######## //Testfall für Schnittstelle 8 (Message/getMessage)
###################################################
if(TESTCASE_8) {
$regUser->identity = "sandra";

echo "<h3>Testfall für Schnittstelle 3 (Message/getMessage)</h3>";



//todo: implementieren

echo '--------Ende des Testfalls --------';
}


?>