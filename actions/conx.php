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
		$data['message'] = $LANG['Welcome'];
	}
	else
		$data['message'] = $LANG['Err_connect_password'];
}
catch (Exception $e) {
	$data['message'] = $e->getMessage();
}


header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);