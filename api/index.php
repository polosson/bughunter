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

extract($_POST);

$response['error']	 = "error";
$response['message'] = "Unknown action! ($action)";

try {

	/**
	 * ACTION GET BUGS LIST
	 */
	if ($action === "get_bugs_list") {
		if (!isset($closed))
			$closed = false;
		$lB = new Liste();
		$bugList = $lB->getListe('t_bugs', '*', 'date', 'DESC', 'closed', '=', (int)$closed);
		if (!$bugList) $bugList = Array();
		$response["data"]	 = $bugList;
		$response["error"]	 = "OK";
		$response["message"] = "Bugs list retreived.";
	}

	/**
	 * ACTION GET SPECIFIC BUG
	 */
	if ($action === "get_bug") {
		if (!isset($id_bug))
			throw new Exception("API::get_bug : missing id_bug!");
		$b = new Bug((int)$id_bug);
		$response["data"]	 = $b->getBugData();
		$response["error"]	 = "OK";
		$response["message"] = "Bug retreived.";
	}

	/**
	 * ACTION COUNT BUGS
	 */
	if ($action === "count_bugs") {
		if (!isset($closed))
			$closed = false;
		$lB = new Liste();
		$bugList = $lB->getListe('t_bugs', '*', 'date', 'DESC', 'closed', '=', (int)$closed);
		if (!$bugList) $bugList = Array();
		$response["data"]	 = count($bugList);
		$response["error"]	 = "OK";
		$response["message"] = "Bugs count retreived.";
	}

	/**
	 * ACTION ADD BUG (need password api_access)
	 */
	if ($action === "insert_bug") {
		if (!isset($password))
			throw new Exception("API: missing password!");
		if ($password !== $api_access)
			throw new Exception("Access denied");
		if (!isset($title))
			throw new Exception("API::insert_bug : missing bug title!");
		if (!isset($description))
			throw new Exception("API::insert_bug : missing bug description!");
		if (strlen($title) < 5 || strlen($title) > 90)
			throw new Exception("API::insert_bug : bug title must be between 5 & 90 characters.");
		if (strlen($description) < 5)
			throw new Exception("API::insert_bug : bug description too short. 5 characters minimum.");
		if (!isset($author) || $author == "")
			$author = "Admin";
		if (!isset($priority) || $priority == "" || $priority < 1 || $priority > 4)
			$priority = 1;
		if (!isset($app_version))
			$app_version = "";
		if (!isset($app_url))
			$app_url = "";

		$bugInfos = Array(
			"author" => $author,
			"app_url" => $app_url,
			"app_version" => $app_version,
			"title" => trim(strip_tags($title)),
			"description" => trim($description),
			"img" => "[]",
			"priority" => (int)$priority,
			"closed" => 0,
			"FK_label_ID" => 0,
			"FK_dev_ID" => 0,
			"FK_comment_ID" => "[]"
		);
		$b = new Bug();
		$b->setBugData($bugInfos);
		$b->save();
		$response['error']	 = "OK";
		$response['message'] = "Bug inserted. Thanks for your report, our devs will take a look at it soon!";
	}
}
catch (Exception $e) {
	$response['message'] = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');					// Allows cross-domain requests
echo ")]}',\n";												// Security against JSONP-injection
echo json_encode($response, JSON_UNESCAPED_UNICODE);