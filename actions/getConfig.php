<?php
require('../init.php');

$data['error']		= 'OK';
$data['countAlive'] = $data['countKilled'] = 0;

try {
	$l = new Liste();
	$data['priorities']  = $PRIORITIES;
	$data['labels']		 = $l->getListe('t_labels', "*", 'id', 'ASC');
	$data['devs']		 = $l->getListe('t_devs', "*", 'id', 'ASC', 'id', '>=', '0');
	$l->getListe('t_bugs', "id", "priority", "DESC", "closed", "=", 0);
	$data['countAlive']  = $l->countResults();
	$l->getListe('t_bugs', "id", "priority", "DESC", "closed", "=", 1);
	$data['countKilled'] = $l->countResults();
	$l->resetFiltre();
	$l->addFiltre('nom', '!=', 'password_access');
	$l->addFiltre('nom', '!=', 'api_access');
	$l->getListe('t_config', '*', 'id', 'ASC');
	$data['globalConf']	 = $l->simplifyList('nom');
}
catch (Exception $e) {
	$data['error']	 = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);