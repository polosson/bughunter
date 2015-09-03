<?php
require('../init.php');

$data['error'] = 'OK';

try {
	extract($_GET);
	if (!isset($type))
		$type = 0;

    $l = new Liste();
	$data['bugsList'] = $l->getListe('t_bugs', "*", "priority", "DESC", "closed", "=", $type, 40);
	$data['priorities'] = $PRIORITIES;
}
catch (Exception $e) {
	$data['error'] = $e->getMessage();
}


header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);