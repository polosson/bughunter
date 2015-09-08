<?php
error_reporting(E_ERROR);

$data['error']	 = 'OK';

try {
	$post = json_decode(file_get_contents("php://input"), true);
	if (!is_array($post))
		throw new Exception("Missing postData.");
	extract($post);

	if (!is_array($sql))
		throw new Exception("Missing SQL credential informations.");

	$configSTR = @file_get_contents("../config/default_config.php");

	if (strlen($configSTR) < 2200)
		throw new Exception("Default config file not found or corrupted.");

	$configSTR = preg_replace('/"HOST", "localhost"/',	'"HOST", "'.$sql['host'].'"', $configSTR);
	$configSTR = preg_replace('/"USER", "user"/',		'"USER", "'.$sql['user'].'"', $configSTR);
	$configSTR = preg_replace('/"PASS", "pass"/',		'"PASS", "'.$sql['pass'].'"', $configSTR);
	$configSTR = preg_replace('/"BASE", "bughunter"/',	'"BASE", "'.$sql['dbnm'].'"', $configSTR);

	if (!file_put_contents("../config/config.php", (string)$configSTR))
		throw new Exception("Can't write config file. Please check permissions of config folder.");
}
catch (Exception $e) {
	$data['error'] = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);