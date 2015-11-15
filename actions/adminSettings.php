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
error_reporting(E_ERROR);
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
		$data['message']  = $LANG['New_label_OK'];
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
		$data['message'] = $LANG['New_dev_OK'];
	}

	if ($action === 'updateSetting') {
		if (!isset($type))
			throw new Exception("Missing the type of setting to update (labels, devs, projectInfo?)");
		if ($type === "projectInfo") {
			$iC = new Infos('t_config');
			foreach($item as $pInfK => $pInfV) {
				if ($pInfK === "password_access") continue;
				if ($pInfK === "api_access") continue;
				$iC->loadInfos('nom', $pInfK);
				$iC->setInfo('value', $pInfV);
				$iC->save('id', 'this', false, false);
			}
		}
		else {
			$i = new Infos('t_'.$type);
			$i->loadInfos('id', $item['id']);
			$i->setAllInfos($item);
			$i->save('id', 'this', false, false);
		}
		$data['error'] = "OK";
		$data['message'] = "OK! '$type' ".$LANG['Updated'];
	}

	if ($action === 'deleteSetting') {
		if (!isset($type))
			throw new Exception("Missing the type of setting to delete (labels, devs, projectInfo?)");
		$i = new Infos('t_'.$type);
		$i->loadInfos('id', $itemID);
		$i->delete();
		$data['error'] = "OK";
		$data['message'] = $LANG['Item_remove_OK']." $type.";
	}

	if ($action === 'updatePW') {
		if (!isset($newPW))
			throw new Exception("Missing the new password!");
		if (strlen($newPW) < 4)
			throw new Exception($LANG['Err_PW_too_short']);
		$newPass = md5(PASSWORD_SALT.$newPW);
		$iC = new Infos('t_config');
		$iC->loadInfos('nom', 'password_access');
		$iC->setInfo('value', $newPass);
		$iC->save('id', 'this', false, false);
		$data['error'] = "OK";
		$data['message'] = $LANG['Password_change_OK'];
	}

	if ($action === 'updateLanguage') {
		if (!isset($newLang))
			throw new Exception("Missing language to change!");
		$iC = new Infos('t_config');
		$iC->loadInfos('nom', 'language');
		$iC->setInfo('value', $newLang);
		$iC->save('id', 'this', false, false);
		$data['error'] = "OK";
		$data['message'] = $LANG['Language_change_OK'];
	}

} catch (Exception $e) {
	$data['message'] = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);
