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
		$post = $_POST;
	if (!is_array($post))
		throw new Exception("Missing postData.");
	extract($post);

	if ($action === 'addBug') {
		$b = new Bug();
		$b->setBugData($bugInfos);
		$b->save();
		$b->notify('new');
		$data['bug']	 = $b->getBugData();
		$data['error']	 = "OK";
		$data['message'] = $LANG['New_bug_OK'];
	}

	if ($action === 'modBug') {
		if (!isset($bugID))
			throw new Exception("modBug: bug's ID is missing!");
		$b = new Bug((int)$bugID);
		$b->setBugData($bugInfos);
		$b->save();
		// @TODO : notify if dev assignment has changed
		$data['bug']	 = $b->getBugData();
		$data['error']	 = "OK";
		$data['message'] = $LANG['Update_bug_OK'];
	}

	if ($action === 'killBug') {
		if (!isset($bugID))
			throw new Exception("killBug: bug's ID is missing!");
		$b = new Bug((int)$bugID);
		$b->killBug();
		$b->notify('close');
		$data['error']	 = "OK";
		$data['message'] = $LANG['Kill_bug_OK'];
	}

	if ($action === 'removeBug') {
		if (!isset($bugID))
			throw new Exception("removeBug: bug's ID is missing!");
		$b = new Bug((int)$bugID);
		$b->removeBug();
		$data['error']	 = "OK";
		$data['message'] = $LANG['Delete_bug_OK'];
	}

	if ($action === 'addComm') {
		if (!isset($bugID))
			throw new Exception("addComm: bug's ID is missing!");
		$b = new Bug((int)$bugID);
		$data['newComment'] = $b->addComment($commentText);
		$b->notify('comment');
		$data['error']	 = "OK";
		$data['message'] = $LANG['New_comment_OK'];
	}

	if ($action === 'modComm') {
		if (!isset($bugID))
			throw new Exception("modComm: bug's ID is missing!");
		if (!is_array($comment))
			throw new Exception("modComm: 'comment' is not an array!");
		$b = new Bug((int)$bugID);
		$b->updateComment((int)$comment['id'], $comment['message']);
		$data['error']	 = "OK";
		$data['message'] = $LANG['Update_comment_OK'];
	}

	if ($action === 'delComm') {
		if (!isset($bugID))
			throw new Exception("delComm: bug's ID is missing!");
		if (!isset($commID))
			throw new Exception("delComm: Comment ID is missing!");
		$b = new Bug((int)$bugID);
		$b->deleteComment((int)$commID);
		$data['error']	 = "OK";
		$data['message'] = $LANG['Delete_comment_OK'];
	}

	if ($action == 'delImg') {
		if (!isset($bugID))
			throw new Exception("delImg: bug's ID is missing!");
		if (!isset($imgName))
			throw new Exception("delImg: Image filename is missing!");
		$b = new Bug((int)$bugID);
		$b->deleteImage($imgName);
		$data['error']	 = "OK";
		$data['message'] = $LANG['Image_delete_OK'];
	}

	if ($action == 'uploadImg') {
		if (!isset($bugID))
			throw new Exception("uploadImg: bug's ID is missing!");
		$fname = $_FILES['file']['name'];
		if (file_exists(DATA_PATH.$fname))
			$fname = date('Ymd-His_').$fname;
		if (!move_uploaded_file($_FILES['file']['tmp_name'], DATA_PATH.$fname))
			throw new Exception("Uploaded file could not be moved to data folder.");

		$image = new SimpleImage(DATA_PATH.$fname);
		if ($image->getWidth() > 1200) {
			$image->resizeToWidth(1200);
			$image->save(DATA_PATH.$fname);
		}

		if ($bugID !== "newBug") {
			$b = new Bug((int)$bugID);
			$b->addImage($fname);
		}
		$data['img']	 = $fname;
		$data['error']	 = "OK";
		$data['message'] = $LANG['Image_add_OK'];
	}
}
catch (Exception $e) {
	$data['message'] = $e->getMessage();
	$data['trace']	 = $e->getTrace();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);