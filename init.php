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
session_start();
// INSTALL PATH
define ("INSTALL_PATH", __DIR__."/");
// INCLUDES
$pathClass = INSTALL_PATH."classes";
$pathConf = INSTALL_PATH."config";
$pathLang = INSTALL_PATH."language";
set_include_path( get_include_path() .
	PATH_SEPARATOR . $pathClass .
	PATH_SEPARATOR . $pathConf  .
	PATH_SEPARATOR . $pathLang
);
// DATA PATH (for uploaded images storage)
define('DATA_PATH', INSTALL_PATH."data/screens/");

// CONFIG
if (!is_file("$pathConf/config.php"))
	die('{"error":"Configuration file is missing! Please check if \'config/config.php\' exists."}');
require_once("config.php");

// AUTOLOAD
function autoload ($classname) { require_once($classname.'.class.php'); }
spl_autoload_register ('autoload');

// PDO INIT
define("DSN", 'mysql:dbname='.BASE.';host='.HOST);
try {
	$bdd = new PDO(DSN, USER, PASS, array(PDO::ATTR_PERSISTENT => true));
	$bdd->query("SET NAMES 'utf8'");
	$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	global $bdd;
}
catch (Exception $e) {
	die('{"error":"PDO connection error: '.$e->getMessage().'"}');
}

// CHECK IF ADMIN SESSION STILL ACTIVE
try {
	$iC = new Infos('t_config');
	$iC->loadInfos('nom', 'password_access');
	$pw = $iC->getInfos('value');

	$authAdmin = false;
	if (isset($_SESSION['authAdmin'])) {
		if ($_SESSION['authAdmin'] === PASSWORD_SALT.$pw)
			$authAdmin = true;
	}
	elseif (isset($_COOKIE['catch_bug'])) {
		if ($_COOKIE['catch_bug'] === PASSWORD_SALT.$pw) {
			$_SESSION['authAdmin'] = PASSWORD_SALT.$pw;
			$authAdmin = true;
		}
	}
	$iC->loadInfos('nom', 'api_access');
	$api_access = $iC->getInfos('value');

	$iC->loadInfos('nom', 'language');
	$language = $iC->getInfos('value');
	if (is_file($pathLang."/$language.php"))
		include("$language.php");
	else include("English.php");
}
catch(Exception $e) {  }


/***************************** MISC FUNCTIONS *********************************/

// GET CURRENT URL OF BUGHUNTER
function get_url() {
	$https = !empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 ||
			!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
			strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0;
	return
		($https ? 'https://' : 'http://').
		(!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
		(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
		($https && $_SERVER['SERVER_PORT'] === 443 ||
		$_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
		substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
}