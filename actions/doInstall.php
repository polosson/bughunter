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

$data['error']	 = 'OK';

try {
	$post = json_decode(file_get_contents("php://input"), true);
	if (!is_array($post))
		throw new Exception("Missing postData.");
	extract($post);

	if (!isset($step))
		throw new Exception("Step of installation is missing.");

	/**
	 * FIRST STEP : copy config file and fill MySQL infos
	 */
	if ($step === 1) {
		if (!is_array($infos))
			throw new Exception("Missing SQL connection informations.");

		$configSTR = @file_get_contents("../config/default_config.php");

		if (strlen($configSTR) < 2200)
			throw new Exception("Default config file not found or corrupted.");

		$configSTR = preg_replace('/"HOST", "localhost"/',	'"HOST", "'.$infos['host'].'"', $configSTR);
		$configSTR = preg_replace('/"USER", "user"/',		'"USER", "'.$infos['user'].'"', $configSTR);
		$configSTR = preg_replace('/"PASS", "pass"/',		'"PASS", "'.$infos['pass'].'"', $configSTR);
		$configSTR = preg_replace('/"BASE", "bughunter"/',	'"BASE", "'.$infos['dbnm'].'"', $configSTR);

		if (!file_put_contents("../config/config.php", (string)$configSTR))
			throw new Exception("Can't write config file. Please check permissions of config folder.");

		$data['nextStep'] = 2;
		$data['message']  = "Configuration file created.";
	}
	/**
	 * Create database if doesn't exists, with its default structure
	 */
	elseif ($step === 2) {
		require('../config/config.php');
		$DBexists	= false;
		$DBstructOk = false;
		try {
			$bddTest = new PDO('mysql:dbname='.BASE.';host='.HOST, USER, PASS);
			$bddTest->query("SET NAMES 'utf8'");
			$bddTest->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$DBexists = true;
			$q = $bddTest->prepare("SELECT * FROM `t_config`");
			$q->execute();
			if ($q->rowCount() >= 1)
				$DBstructOk = true;
		}
		catch (Exception $e) {
			if ($e->getCode() !== 1049 && $e->getCode() !== '42S02')
				throw new Exception("errCode '".$e->getCode()."' -> PDO connection error: ".$e->getMessage());
		}

		if (!$DBexists) {
			$bddCreate = new PDO('mysql:host='.HOST, USER, PASS, array(PDO::ATTR_PERSISTENT => true));
			$bddCreate->query("SET NAMES 'utf8'");
			$bddCreate->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$bddCreate->query("CREATE DATABASE `".BASE."`");
		}
		if (!$DBstructOk) {
			$pathDefaultSQL = realpath(__DIR__."/../config/default_DB.sql");
			$command='mysql -h'.HOST.' -u'.USER.' -p'.PASS.' '.BASE.' < '.$pathDefaultSQL;
			$resp = shell_exec($command);
			if ($resp)
				throw new Exception("SQL import failed. Please do it manually.");
		}
		$data['nextStep'] = 3;
		$data['message']  = "Database created.";
		if ($DBexists && $DBstructOk)
			$data['message'] = "The database '".BASE."' already exists!";
	}
	/**
	 * Give some informations about the project related to bughunter
	 */
	elseif ($step === 3) {
		if (!is_array($infos))
			throw new Exception("Missing SQL connection informations.");
		require('../init.php');
		$iC = new Infos('t_config');
		foreach($infos as $k => $v) {
			$iC->loadInfos('nom', $k);
			if ($k === 'password_access')
				$iC->setInfo('value', md5(PASSWORD_SALT.$v));
			else
				$iC->setInfo('value', $v);
			$iC->save('id', 'this', false, false);
		}
		$data['nextStep'] = 4;
		$data['message']  = "Project informations saved.";
	}
	else
		throw new Exception("Unkown installation step!");
}
catch (Exception $e) {
	$data['error'] = $e->getMessage();
}

header('HTTP/1.1 200 OK');
header('Content-type: application/json; charset=UTF-8');
echo ")]}',\n"; // Pour sécu anti injection JSONP
echo json_encode($data, JSON_UNESCAPED_UNICODE);