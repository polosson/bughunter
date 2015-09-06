<?php
require('../init.php');

$data['auth']	 = 'error';
$data['message'] = '';

try {
	$iC = new Infos('t_config');
	$iC->loadInfos('nom', 'password_access');
	$pw = $iC->getInfos('value');

	$post = json_decode(file_get_contents("php://input"), true);
	if (!is_array($post))
		throw new Exception("Missing password postData.");
	extract($post);

	if (md5(PASSWORD_SALT.$passw) == $pw) {
		$_SESSION['authAdmin'] = PASSWORD_SALT.$pw;
		setcookie('catch_bug', PASSWORD_SALT.$pw, time() + 15*24*3600, "/", null, false, false); // Durée du cookie : 15 jours

		$data['auth']	 = 'OK';
		$data['message'] = "Welcome dear Admin!";
	}
	else
		$data['message'] = "Wrong password. Please try again.";
}
catch (Exception $e) {
	$data['message'] = $e->getMessage();
}


header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);