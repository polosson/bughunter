<?php
// INSTALL PATH
define ("INSTALL_PATH", __DIR__."/");
// INCLUDES
$pathClass = INSTALL_PATH."classes";
$pathConf = INSTALL_PATH."config";
set_include_path( get_include_path() .
	PATH_SEPARATOR . $pathClass .
	PATH_SEPARATOR . $pathConf
);
// CONFIG
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
	die('<strong style="color:red;">Erreur de connexion PDO : '.$e->getMessage().'</strong>');
}