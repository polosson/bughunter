<?php
session_start();
require('../init.php');

$data['auth']	 = 'error';
$data['message'] = '';

try {
	$post = json_decode(file_get_contents("php://input"), true);
	if (!is_array($post))
		throw new Exception("Missing password postData.");
	extract($post);

	if (md5($passw) == MAIN_PASSWORD) {

		// TODO : add session & Cookie

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