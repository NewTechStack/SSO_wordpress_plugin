<?php

require __DIR__ . '/wp-load.php';


if (!isset($_GET['uri'])) {
 exit();
}
$uri = $_GET['uri'];
$data = file_get_contents($uri, true);
$datajson = json_decode($data, true);

$backend = get_option('sso_back');
$frontend = get_option('sso_front');
$key = get_option('sso_key');

$request_key = $datajson['data']['key'];
$request_secret = $datajson['data']['secret'];

$url = $backend . 'extern/key/' . $request_key . '/token';
$data = array('apitoken' => $key, "secret" => $request_secret );
$postdata = json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
$result = curl_exec($ch);
if ($result == false) {
	echo curl_error($ch);
}
curl_close($ch);
$datajson = json_decode($result, true);
$data = $datajson['data'];
if ( is_null($data['error']) {
$token = $data['usrtoken'];
echo $token;
exit();
$part = explode(".",$token);

$header = $part[0];
$payload = $part[1];
$signature = $part[2];

$encodedData = $signature;

// ===== Open public path and return this in string format

//$fp = fopen($keyPublicPath,"r");
$chavePublicaString = fread($fp,8192);
//fclose($fp);

// ===== Open public key string and return 'resourse'

$resPublicKey = openssl_get_publickey($chavePublicaString);

// ===== If any openssl error occurs, print it

$openSSLError = false;
if($debug['print-openssl-errors']){
    while($msg = openssl_error_string()){
        echo $msg . "\n";
        $openSSLError = true;
    }
}

// ===== See details of a public key

if($debug['print-key-details']){
    $keyPublicDetails = openssl_pkey_get_details($resPublicKey);

    echo "Public Key Details:\n";
    echo print_r($keyPublicDetails,true)."\n";
}

// ===== Decode base64 to reaveal dots (Dots are used in JWT syntaxe)

$encodedData = base64_decode($encodedData);

if($debug['print-msgs']){
    echo "Encrypted signature: ".$encodedData."\n";
}

// ===== Decrypt data in parts if necessary. Using dots as split separator.

$rawEncodedData = $encodedData;

$countCrypt = 0;
$partialDecodedData = '';
$decodedData = '';
$split2 = explode('.',$rawEncodedData);
foreach($split2 as $part2){
    $part2 = base64_decode($part2);
    
    if($debug['print-openssl-crypt']){
        $countCrypt++;
        echo "CRYPT PART ".$countCrypt.": ".$part2."\n";
    }
    
    openssl_public_decrypt($part2, $partialDecodedData, $resPublicKey);
    $decodedData .= $partialDecodedData;
}

// ===== Print data decrypted

if($debug['print-msgs']){
    echo "Decrypted signature: ".$decodedData."\n";
}

// ===== If any openssl error occurs, print it

$openSSLError = false;
if($debug['print-openssl-errors']){
    while($msg = openssl_error_string()){
        echo $msg . "\n";
        $openSSLError = true;
    }
}

// ===== Validate JWT

if($debug['print-msgs']){
    echo "\nFINISH VALIDATE JWT!\n\n";
}

if($header.".".$payload === $decodedData){
    echo "VALID JWT!\n\n";
    
    $payload = base64_decode($payload);
    $payload = json_decode($payload,true);
    
    echo "Payload:\n";
    echo print_r($payload,true);
} else {
    echo "INVALID JWT!";
}
}

echo 'wnd.close();';



?>
