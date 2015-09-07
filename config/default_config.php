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

/**
	This file is an EXAMPLE file for bughunter's configuration.

	It must be copied it in the same directory, and named "config.php"
	in order to make the bughunter to work.
 */

/**
 *  MySQL DATABASE CREDENTIALS
 */
define ("HOST", "localhost");				// MySQL host
define ("USER", "user");					// MySQL user
define ("PASS", "pass");					// MySQL password
define ("BASE", "SaAM_bughunter");			// MySQL database name

/**
 * FIELD PREFIX USED TO INDICATE THE EXISTENCE OF A RELATIONSHIP (JOIN)
 */
define ("FOREIGNKEYS_PREFIX",	"FK_");
/**
 *  LIST OF TABLES RELATIONSHIPS
 */
$RELATIONS = Array(
	"FK_label_ID"	=> Array('table' => "t_labels",		'alias' => 'label'),
	"FK_dev_ID"		=> Array('table' => "t_devs",		'alias' => 'dev'),
	"FK_comment_ID"	=> Array('table' => "t_comments",	'alias' => 'comment'),
	"FK_bug_ID"		=> Array('table' => "t_bugs",		'alias' => 'bug')
);

/**
 *  STRING FOR PASSWORDS EXTENSION (SALT)
 */
define("PASSWORD_SALT",		md5('ax84yp52d9z*'));

/**
 *  FIELD NAME USED FOR 'LAST ACTION' DATE
 */
define ("LAST_UPDATE",		"last_action");
/**
 *  FIELD NAME USED FOR 'CREATION' DATE
 */
define ("DATE_CREATION",	"date_creation");

/**
 *  FIELDS NAMES THAT SHOULD BE PARSED TO ISO 8601 DATE FORMAT (JS COMPATIBILITY)
 */
$DATE_FIELDS = Array(
	"date",
	"last_action",
	"date_creation"
);

/**
 *  LIST OF PRIORITIES AND THEIR COLORS TO BE USED IN BUGHUNTER
 */
$PRIORITIES = Array(
	"4" => Array('priority'=>4, 'color'=>'#E61317'),
	"3" => Array('priority'=>3, 'color'=>'#F85604'),
	"2" => Array('priority'=>2, 'color'=>'#EF9D17'),
	"1" => Array('priority'=>1, 'color'=>'#E9C414')
);