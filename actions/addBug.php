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

$data['error']	 = "error";
$data['message'] = "";

try {
	if (!$authAdmin)
		throw new Exception("Access denied. Please login as an Admin to continue.");
	
	$data['bug']	 = Array('id'=>"1000", 'priority'=>"4", 'title'=>'TODO', 'description'=>'This is a fake bug to test.', 'closed'=>'0', 'date'=>'2015-09-09T00:39:02+02:00');
	$data['error']	 = "OK";
	$data['message'] = "Bug added to the list. Thanks for your report.";
}
catch (Exception $e) {
	$data['message'] = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);