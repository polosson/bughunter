<?php
require('../init.php');

$data['error'] = 'OK';

try {
	extract($_GET);
	if (!isset($type))
		$type = 0;

    $l = new Liste();
	$data['priorities'] = $PRIORITIES;
	$data['labels']		= $l->getListe('t_labels', "*", 'id', 'ASC');
	$data['devs']		= $l->getListe('t_devs', "*", 'id', 'ASC', 'id', '>=', '0');
	$data['bugsList']	= $l->getListe('t_bugs', "*", "priority", "DESC", "closed", "=", $type, 40);
}
catch (Exception $e) {
	$data['error'] = $e->getMessage();
}


header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);