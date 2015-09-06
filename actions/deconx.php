<?php
session_start();
$_SESSION['authAdmin'] = false;
session_destroy();

unset($_COOKIE['catch_bug']);
setcookie('catch_bug','', time() - 3600,'/');

$data['message'] = 'See you, dear Admin!';

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);