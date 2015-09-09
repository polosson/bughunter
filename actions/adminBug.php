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
error_reporting(E_ERROR);
require('../init.php');

$data['error']	 = "error";
$data['message'] = "Unknown action!";

try {
	if (!$authAdmin)
		throw new Exception("Access denied. Please login as an Admin to continue.");

	$post = json_decode(file_get_contents("php://input"), true);
	if (!is_array($post))
		throw new Exception("Missing password postData.");
	extract($post);

	if ($action === 'addBug') {
		$b = new Bug();
		$b->setBugData($bugInfos);
		$b->save();
		$data['bug']	 = $b->getBugData();
		$data['error']	 = "OK";
		$data['message'] = "Bug added to the list. Thanks for your report.";
	}


	if ($action === 'killBug') {
		if (!isset($bugID))
			throw new Exception("killBug: bug's ID is missing!");
		$b = new Bug((int)$bugID);
		$b->killBug();
		$data['error']	 = "OK";
		$data['message'] = "Bug closed.";
	}

	if ($action === 'removeBug') {
		if (!isset($bugID))
			throw new Exception("removeBug: bug's ID is missing!");
		$b = new Bug((int)$bugID);
		$b->removeBug();
		$data['error']	 = "OK";
		$data['message'] = "Bug deleted.";
	}
}
catch (Exception $e) {
	$data['message'] = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);