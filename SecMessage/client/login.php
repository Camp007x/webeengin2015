<?php
error_reporting(E_ALL);
	include "classes.php";
	
	$regUser = new User();
	//$regUser->identity = $_POST['username'];
	$regUser->identity = 'dietertt12ggg3';
	//$regUser->setPassword($_POST['password']);
	#$regUser->setPassword('password');
	#$regUser->generateSaltMasterkey();
	#$regUser->calculateMasterKey();
	#$regUser->generateKeys();
	/*
	$postdata = http_build_query(
		array(
			'data' => json_encode($regUser)
		)
	);
*/

	$opts = array('http' =>
		array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/json',
			//'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => json_encode($regUser)
		)
	);

	$context  = stream_context_create($opts);
	
	$result = file_get_contents( "http://fh.thomassennekamp.de/server/user", false, $context);

	print_r($result);
?>
