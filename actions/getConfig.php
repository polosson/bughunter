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
	$data['available_languages'] = Array();
	foreach (glob($pathLang.'/*') as $langFile) {
		if (is_dir($langFile)) continue;
		$data['available_languages'][] = preg_replace('/\.php/', '', basename($langFile));
	}
	$data['LANG'] = $LANG;
}
catch (Exception $e) {
	$data['error']	 = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);