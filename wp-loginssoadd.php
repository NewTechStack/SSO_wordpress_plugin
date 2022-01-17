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
$register = get_option('sso_register');
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
if ( !is_null($data['error']) ) {
	exit();
}
$token = $data['usrtoken'];

$url = $backend . 'extern/public';
$data = array('apitoken' => $key);
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

$part = explode(".",$token);

$header = $part[0];
$payload = $part[1];
$signature = $part[2];
$encodedData = $signature;


if(true){
    $payload = base64_decode($payload);
    $payload = json_decode($payload,true);
    echo print_r($payload,true);
    echo $register;
}

echo 'wnd.close();';

?>
