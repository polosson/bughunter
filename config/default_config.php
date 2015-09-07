<?php
// MySQL DATABASE
define ("HOST", "localhost");							// Nom de l'hôte MySQL
define ("USER", "user");								// Utilisateur SQL
define ("PASS", "pass");								// Mot de passe SQL
define ("BASE", "SaAM_bughunter");						// Nom de la base de données

// JSON DATABASE
define ("JSON_DATA_PATH", INSTALL_PATH."data/JDB/");	// Chemin pour les données JSON (inutile ici)

// JOINTURES DE TABLES SQL
define ("FOREIGNKEYS_PREFIX", "FK_");					// Préfixe utilisé pour signaler une relation de champ (jointure)
$RELATIONS = Array(										// Liste des relations entre les tables
	"FK_label_ID"	=> Array('table' => "t_labels",		'alias' => 'label'),
	"FK_dev_ID"		=> Array('table' => "t_devs",		'alias' => 'dev'),
	"FK_comment_ID"	=> Array('table' => "t_comments",	'alias' => 'comment'),
	"FK_bug_ID"		=> Array('table' => "t_bugs",		'alias' => 'bug')
);

// NOM DU CHAMP POUR LA DATE DE DERNIÈRE MODIFICATION
define ("LAST_UPDATE", "last_action");
// NOM DU CHAMP POUR LA DATE DE CRÉATION
define ("DATE_CREATION", "date_creation");

// Nom des champs qui peuvent etre formattés en date ISO 8601
$DATE_FIELDS = Array(
	"date",
	"last_action",
	"date_creation"
);


/******************************** CUSTOM GLOBALS *******************************/

$PRIORITIES = Array(
	"4" => Array('priority'=>4, 'color'=>'#E61317'),
	"3" => Array('priority'=>3, 'color'=>'#F85604'),
	"2" => Array('priority'=>2, 'color'=>'#EF9D17'),
	"1" => Array('priority'=>1, 'color'=>'#E9C414')
);

define("PASSWORD_SALT",	md5('ax84yp52d9z*'));