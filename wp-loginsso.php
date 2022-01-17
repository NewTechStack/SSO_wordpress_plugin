
<?php
require __DIR__ . '/wp-load.php';

$backend = esc_attr( get_option('sso_back') );
$frontend = esc_attr( get_option('sso_front') );
$key = esc_attr( get_option('sso_key') ) ;

$url = $backend . 'extern/key';
$data = array('apitoken' => $key, "valid_until" => 180, "asked" => array( 0 => 'username' ) );

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
$login_url = $frontend . '/sso/extern/' . $data['key'] . '/' . $data['auth'] . '/accept';

$tmpfname = tempnam("/tmp", "FOO");

$tmp = fopen($tmpfname, "w");
fwrite($tmp, $result );
fclose($tmp);

echo '<HTML><head></head><body>';
echo '<script> let wnd = window.open("' . $login_url . '", "test", "height=700,width=700"); </script>';
echo '<script src="/wp-loginssoadd.php?uri=' . $tmpfname . '"></script>';
echo '</body></HTML>';

?>
