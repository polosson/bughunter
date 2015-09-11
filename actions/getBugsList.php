<?php
/**
	Copyright (C) 2015  Azuk & Polosson

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Affero General Public License as
	published by the Free Software Foundation, either version 3 of the
	License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Affero General Public License for more details.

	You should have received a copy of the GNU Affero General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
require('../init.php');

$data['error'] = 'OK';

try {
	extract($_GET);
	if (!isset($type))
		$type = 0;

    $l = new Liste();
	$bugList = $l->getListe('t_bugs', "*", "priority", "DESC", "closed", "=", $type);
	if (!$bugList) $bugList = Array();
	$data['bugsList'] = $bugList;
}
catch (Exception $e) {
	$data['error'] = $e->getMessage();
	$data['trace'] = $e->getTrace();
}


header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);