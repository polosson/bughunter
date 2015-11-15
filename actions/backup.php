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

	$dump = new DumpSQL();
	$sqlFile = $dump->getDumpFile();
	$zipFile = "data/backup_".BASE."_".date('Y-m-d').".zip";
	$options = array('remove_all_path'=>true, 'add_path'=>'screens/');
	$zip = new ZipArchive();
	$zip->open(INSTALL_PATH.$zipFile, ZipArchive::CREATE|ZipArchive::OVERWRITE);
	$zip->addFile(INSTALL_PATH.$sqlFile, basename($sqlFile));
	$zip->addGlob(DATA_PATH.'*', null, $options);
	$zip->close();
	unlink(INSTALL_PATH.$sqlFile);
	$data['error'] = 'OK';
	$data['message'] = $LANG['Backup_OK'];
	$data['dumpfile'] = $zipFile;

} catch (Exception $e) {
	$data['message'] = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);
