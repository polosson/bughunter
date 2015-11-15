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
$data['message'] = "Unknown error";

try {
	if (!$authAdmin)
		throw new Exception("Access denied. Please login as an Admin to continue.");

	$bdd->query("TRUNCATE `t_bugs`; TRUNCATE `t_comments`;");
	$iC = new Infos('t_config');
	$iC->loadInfos('nom', 'project_name');
	$iC->setInfo('value', 'Your project');
	$iC->save('id', 'this', false, false);
	$iC->loadInfos('nom', 'git_repo');
	$iC->setInfo('value', 'git://your/git/repo/url.git');
	$iC->save('id', 'this', false, false);
	$iC->loadInfos('nom', 'project_type');
	$iC->setInfo('value', 'open-source');
	$iC->save('id', 'this', false, false);
	foreach(glob(DATA_PATH.'*') as $screen) {
		if (is_dir($screen)) continue;
		unlink($screen);
	}
	$data['error'] = 'OK';
	$data['message'] = $LANG['Reset_OK'];

} catch (Exception $e) {
	$data['message'] = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);
