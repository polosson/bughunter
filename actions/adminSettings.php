<?php
/**
  Copyright (C) 2015  Polosson

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

$data['error'] = "error";
$data['message'] = "Unknown action!";

try {
	if (!$authAdmin)
		throw new Exception("Access denied. Please login as an Admin to continue.");

	$post = json_decode(file_get_contents("php://input"), true);
	if (!is_array($post))
		throw new Exception("Missing postData.");
	extract($post);

	if ($action === 'addLabel') {
		if (!is_array($label))
			throw new Exception("addLabel: Label must be an array!");
		$iL = new Infos('t_labels');
		$iL->setInfo('name', $label['name']);
		$iL->setInfo('color', $label['color']);
		$iL->save('id', 'this', false, false);
		$data['newLabel'] = $iL->getInfos();
		$data['error']	  = "OK";
		$data['message']  = "Label added.";
	}

	if ($action === 'addDev') {
		if (!is_array($dev))
			throw new Exception("addDev: Dev must be an array!");
		$iD = new Infos('t_devs');
		$iD->setInfo('pseudo', $dev['pseudo']);
		$iD->setInfo('mail', $dev['mail']);
		$iD->save('id', 'this', false, false);
		$data['newDev']  = $iD->getInfos();
		$data['error']	 = "OK";
		$data['message'] = "Dev added.";
	}

	if ($action === 'updateSetting') {
		if (!isset($type))
			throw new Exception("Missing the type of setting to update (label, dev, password, project?)");

		$data['error'] = "OK";
		$data['message'] = "$type updated.";
	}

	if ($action === 'updatePW') {
		if (!isset($newPW))
			throw new Exception("Missing the new password!");
		if (strlen($newPW) < 4)
			throw new Exception("New password is too short!");
		$newPass = md5(PASSWORD_SALT.$newPW);
		$iC = new Infos('t_config');
		$iC->loadInfos('nom', 'password_access');
		$iC->setInfo('value', $newPass);
		$iC->save('id', 'this', false, false);
		$data['error'] = "OK";
		$data['message'] = "Main password updated.";
	}

} catch (Exception $e) {
	$data['message'] = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);
