<?php
require('../init.php');

$data['error']		= 'OK';
$data['countAlive'] = $data['countKilled'] = 0;

try {
	$l = new Liste();
	$l->getListe('t_bugs', "id", "priority", "DESC", "closed", "=", 0);
	$data['countAlive']  = $l->countResults();
	$l->getListe('t_bugs', "id", "priority", "DESC", "closed", "=", 1);
	$data['countKilled'] = $l->countResults();
}
catch (Exception $e) {
	$data['error']	 = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);