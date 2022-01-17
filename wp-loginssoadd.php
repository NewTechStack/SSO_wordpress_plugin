<?php
if (!isset($_GET['uri'])) {
 exit();
}

$uri = $_GET['uri'];
$data = file_get_contents($uri, true);
$datajson = json_decode($data, true);

$backend = esc_attr( get_option('sso_back') );
$frontend = esc_attr( get_option('sso_front') );
$key = esc_attr( get_option('sso_key') ) ;

$url = $backend . 'extern/key/' . $request_key . '/token';
$data = array('apitoken' => $key, "secret" => '' );
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

echo $result;
?>
