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

class DumpSQL {

	/**
	 * @var OBJECT PDO Instance for SQL connexion
	 */
	protected $bddCx;
	/**
	 * @var STRING|ARRAY tables list to dump
	 */
	private $tables;
	/**
	 * @var STRING result of the SQL dump
	 */
	private $dump;

	/**
	 * BACKUP DATABASE
	 * @param STRING|ARRAY $tables Tables names (comma separated, or array) or '*' for all tables
	 * @param OBJECT $pdoInstance A pre-initialized PDO instance (optionnal)
	 */
	public function __construct ($tables='*', $pdoInstance=false) {
		if (!$pdoInstance || !is_object($pdoInstance))
			$this->initPDO();
		else
			$this->bddCx = $pdoInstance;
		$this->tables = $tables;
		$this->createDump();
	}

	/**
	 * Save dump result in a file and get it's path
	 * @param STRING $filename Name of the file (if not specified, will be "backup_dbName_YY-MM-DD.sql")
	 * @return Dump file's name & path
	 */
	public function getDumpFile ($filename=false) {
		if (!$filename)
			$filename = "backup_".BASE."_".date('Y-m-d');
		$filepath = 'data/'.$filename.'.sql';
		file_put_contents(INSTALL_PATH.$filepath, $this->dump);
		return $filepath;
	}

	/**
	 * Save dump result in a string and get it
	 * @return Dump content as string
	 */
	public function getDumpString () {
		return $this->dump;
	}


	///////////////////////////////////////////////////////////// METHODES PRIVÉES /////////////////////////////////////////////////////

	/**
	 * Creation of the SQL dump
	 */
	private function createDump () {
		$this->dump = '';
		if ($this->tables === '*') {
			$q = $this->bddCx->prepare('SHOW TABLES');
			$q->execute();
			$tables = $q->fetchAll(PDO::FETCH_COLUMN);
		}
		else
			$tables = is_array($this->tables) ? $this->tables : explode(',', $this->tables);
		if (count($tables) == 0)
			throw new Exception("No table found in this database.");

		$this->dump = "\n-- SQL DATABASE DUMP --\n";
		$this->dump .= "-- DATE: ".date('Y-m-d')."\n\n";
		$this->dump .= 'SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";';
		$this->dump .= "\n\n";

		foreach($tables as $table) {
			$c = $this->bddCx->prepare('SHOW CREATE TABLE `'.$table.'`');			// Get table structure
			$c->execute();
			$resultCreate = $c->fetchAll(PDO::FETCH_ASSOC);

			$r = $this->bddCx->prepare("SELECT * FROM $table");						// Get table data
			$r->execute();
			$resultTable = $r->fetchAll(PDO::FETCH_ASSOC);
			$nbRec = count($resultTable);

			$this->dump .= "-- ----------------- TABLE '$table' ------------------------\n\n";
			$this->dump .= "DROP TABLE IF EXISTS `$table`;\n\n";
			$this->dump .= $resultCreate[0]['Create Table'].";\n\n";

			if ($nbRec > 0) {
				$this->dump .= "INSERT INTO `$table` VALUES ";
				$countRec = 0;
				foreach ($resultTable as $row) {
					$countRec++ ;
					$this->dump .= "\n(";
					$nbVal = count($row);
					$countVal = 0;
					foreach ($row as $value) {
						$countVal++ ;
						$value = addslashes($value);
						$value = preg_replace("/\\r\\n/", "/\\\r\\\n/", $value);
						if (isset($value)) $this->dump .= "'$value'" ;
						else $this->dump .= "''";
						if ($countVal == $nbVal) {
							$this->dump .= ")";
							if ($countRec == $nbRec) $this->dump .= ";";
							else $this->dump .= ",";
						}
						else $this->dump .= ",";
					}
				}
			}
			$this->dump .= "\n\n\n";
		}
	}

	/**
	 * Initialization of PDO object if doesn't exists yet
	 */
	protected function initPDO () {
		$this->bddCx = null;
		$this->bddCx = new PDO(DSN, USER, PASS, array(PDO::ATTR_PERSISTENT => false));
		$this->bddCx->query("SET NAMES 'utf8'");
		$this->bddCx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
}