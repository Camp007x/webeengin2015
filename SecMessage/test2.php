
<?php
// Create the keypair
$res=openssl_pkey_new();

// Get private key
openssl_pkey_export($res, $privkey, "PassPhrase number 1" );

// Get public key
$pubkey=openssl_pkey_get_details($res);
$pubkey=$pubkey["key"];


//echo $privkey;
$adresse[0] = "1";
$adresse[1] = "Thomas";
$adresse[2] = "Scheibenstr.";
$adresse[4] = $pubkey;

#print_r($adresse);

#$save=  urlencode(json_encode($adresse));
#print_r($save);
#var_dump($save);


#echo urlencode($save);

/*
print_r($adresse);
echo "/n";
$save=  json_encode($adresse);
print_r($save);
print_r(json_decode($save));
*/
/*
var_dump($privkey);
var_dump($pubkey);

*/



$postdata = http_build_query(
    array(
        'data' => json_encode($adresse)
    )
);

$opts = array('http' =>
    array(
        'method'  => 'GET',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postdata
    )
);

$context  = stream_context_create($opts);

$result = file_get_contents("http://fh.thomassennekamp.de/test.php", false, $context);
