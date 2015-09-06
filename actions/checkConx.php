<?php
require('../init.php');

$data['error']	 = 'OK';
$data['auth']	 = 'error';
$data['message'] = '';

if ($authAdmin === true) {
	$data['message'] = "Current Admin session still active.";
	$data['auth']	 = "authOK";
}
else
	$data['message'] = "No admin session found.";

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);