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
/**
 * UTILISATION d'une base de données JSON
 */
class JsonDB {

	private $table;
	private $jsonFile;
	private $jsonFileBack;
	private $jtable;
	private $jtableStruct;
	private $jtableData;
	private $nextID;

	/**
	 * UTILISATION d'une base de données JSON
	 * @param STRING $table Le nom de la "table" (fichier json) à utiliser
	 * @param BOOLEAN $createTable TRUE pour créer la table si elle est introuvable (default FALSE : renvoie une erreur si introuvable)
	 * @param ARRAY $createStructure Utile à la création de table seulement : un tableau contenant la liste des colonnes et leur type. Ex : Array("id"=>"int","nom"=>"string"...) (default FALSE)
	 */
	public function __construct($table=false, $createTable=false, $createStructure=false) {
		if ($table == false)
			throw new Exception('JsonDB::(__construct) : Il manque le nom de la table (fichier json) à utiliser');
		$this->table		= $table;
		$this->jsonFile		= JSON_DATA_PATH."$table.json";
		$this->jsonFileBack	= $this->jsonFile.'.bckp';
		// 1er essai : load file
		$jsonStr = @file_get_contents($this->jsonFile);
		$this->jtable = json_decode($jsonStr, true);
		// 2eme essai : load du backup
		if (!$this->jtable || count($this->jtable['structure']) == 0) {
			$jsonStr = @file_get_contents($this->jsonFileBack);
			$this->jtable = json_decode($jsonStr, true);
		}
		// Si toujours rien
		if (!$this->jtable) {
			if (!$createTable)
				throw new Exception("JsonDBD::construct() : La table '$table' n'existe pas, ou le fichier '$table.json' est illisible. Utilisez JsonDB('$table', true, array('id'=>'int','nom'=>'string')) pour la créer");
			$this->createTable($createStructure);
		}
		$this->jtableStruct	= $this->jtable['structure'];
		$this->jtableData	= $this->jtable['data'];
		$this->nextID		= $this->jtable['nextID'];
	}

	/**
	 * Récupère les données d'une table (liste complète)
	 * @param BOOLEAN $resolveJoints TRUE pour récupérer les jointures, FALSE pour garder la valeur de l'entrée non modifiée (default FALSE)
	 * @return ARRAY Un tableau avec toutes les données de la table, réindexé avec les ID des entrées. FALSE si erreur
	 */
	public function getData ($resolveJoints=false) {
		if (!is_array($this->jtableData))
			return false;
		if (!$resolveJoints)
			return array_column($this->jtableData, null, 'id');
		$resolvedData = Array();
		foreach (array_column($this->jtableData, null, 'id') as $id=>$entry)
			$resolvedData[$id] = $this->resolveJointures($entry);
		return $resolvedData;
	}
	/**
	 * Récupère le données d'une table (liste complète), réindexées selon une colonne donnée
	 * @param STRING $key La colonne à utiliser pour réindexer les résultats
	 * @param BOOLEAN $resolveJoints TRUE pour récupérer les jointures, FALSE pour garder la valeur de l'entrée non modifiée (default FALSE)
	 * @return ARRAY Le tableau des résultats, réindexé selon la clé spécifiée
	 */
	public function getDataBy ($key=false, $resolveJoints=false) {
		if (!is_array($this->jtableData))
			return false;
		if (!$key)
			throw new Exception("JsonDB::getDataBy() : Il manque la nom de la colonne à utiliser pour réindexer les résultats");
		if (!array_key_exists($key, $this->jtableStruct))
			throw new Exception("JsonDB::getDataBy() : La colonne '$key' n'existe pas dans la structure de la table '$this->table'");
		if ($this->jtableStruct[$key] != 'integer' && $this->jtableStruct[$key] != 'string' && $this->jtableStruct[$key] != 'date')
			throw new Exception("JsonDB::getDataBy() : La colonne '$key' est de type '".$this->jtableStruct[$key]."', elle ne peut donc pas être utilisée pour réindexer les résultats");
		$reindex = array_column($this->jtableData, null, $key);
		if (count($reindex) != count($this->jtableData))
			throw new Exception("JsonDB::getDataBy() : Les résultats réindexés sont moins nombreux que les données contenues dans la table. Cela est certainement dû au fait que la colonne '$key' n'est pas une clé unique");
		if (!$resolveJoints)
			return $reindex;
		$resolvedData = Array();
		foreach ($reindex as $id=>$entry)
			$resolvedData[$id] = $this->resolveJointures($entry);
		return $resolvedData;
	}
	/**
	 * Compte le nombre d'entrées qu'il y a dans la table
	 * @return INT Le nombre d'entrées que contient la table
	 */
	public function countData () {
		if (!is_array($this->jtableData))
			return 0;
		return count($this->jtableData);
	}
	/**
	 * Récupère UNE SEULE entrée du JSON décodé
	 * @param INT $id Le numéro d'identification de la ligne à récupérer
	 * @param BOOLEAN $resolveJoints TRUE pour récupérer les jointures, FALSE pour garder la valeur de l'entrée non modifiée (default FALSE)
	 * @return ARRAY le résultat de la lecture, ou FALSE si erreur
	 */
	public function getEntry ($id=null, $resolveJoints=false) {
		if (!is_array($this->jtableData))
			return false;
		if ($id === null)
			return false;
		$entryKey = array_search((int)$id, array_column($this->jtableData, 'id'));
		if ($entryKey === false)
			return false;
		if (!$resolveJoints)
			return $this->jtableData[(int)$entryKey];
		return $this->resolveJointures($this->jtableData[(int)$entryKey]);
	}

	/**
	 * REcherche une valeur dans la table colonne - valeur
	 * @param STRING $key La colonne dans laquelle rechercher la valeur
	 * @param STRING $val La valeur à rechercher dans la table (=)
	 * @param BOOLEAN $resolveJoints TRUE pour récupérer les jointures, FALSE pour garder la valeur de l'entrée non modifiée (default FALSE)
	 * @return ARRAY Un tableau avec les données correspondant à la recherche
	 */
	public function searchEntries ($key, $val, $resolveJoints=false) {
		if		($val === "true")  $val = true;
		elseif	($val === "false") $val = false;
		elseif	(ctype_digit((string)$val) && $this->jtableStruct[$key] != 'date') $val = (int)$val;
		$dataFiltred = Array();
		foreach ($this->jtableData as $data){
			if ($data[$key] !== $val)
				continue;
			$dataFiltred[$data['id']] = $data;
		}
		if (!$resolveJoints)
			return $dataFiltred;
		$resolvedData = Array();
		foreach ($dataFiltred as $id=>$entry)
			$resolvedData[$id] = $this->resolveJointures($entry);
		return $resolvedData;
	}

	/**
	 * Résout les jointures d'une entrée
	 * @param ARRAY $entryData Un tableau avec les données complètes de l'entrée
	 * @return ARRAY Un tableau avec les données de l'entrées, dont les jointure résolues (ou pas, si erreur)
	 */
	public function resolveJointures ($entryData=false) {
		if (!is_array($entryData))
			throw new Exception("JsonDB::resoleJointure() : L'entrée spécifiée doit être un tableau des données brutes");
		$dataResolved = Array();
		foreach($entryData as $k => $val) {
			$dataResolved[$k] = $val;
			if (preg_match('/:/', $this->jtableStruct[$k])) {
				$joint = explode(':', $this->jtableStruct[$k]);
				$jointTable  = $joint[0];
				$jointColumn = $joint[1];
				try {
					$jj = new JsonDB($jointTable);
					$rj = $jj->searchEntries($jointColumn, $val);
					if (count($rj) != 0)
						$dataResolved[$k] = array_shift($rj); // Ne récupère QUE le 1er résultat si y'en a plusieurs !
				}
				catch (Exception $e) {}
			}
		}
		return $dataResolved;
	}


	/**
	 * Récupère la dernière ID (pour increment)
	 * @return INT la dernière valeur utilisée en ID
	 */
	public function getLastID() {
		return $this->nextID-1;
	}

	/**
	 * Récupère les infos du fichier de backup
	 */
	public function getBackup() {
		if (!is_file($this->jsonFileBack))
			throw new Exception("JsonDB::loadBackup() : Fichier de backup introuvable");
		$jsonStr  = @file_get_contents($this->jsonFileBack);
		$jsonBckp = json_decode($jsonStr, true);
		if ($jsonBckp == null)
			throw new Exception("JsonDB::loadBackup() : Données de la table du backup corrompues");
		$backup = Array();
		foreach ($jsonBckp['data'] as $data){
			$backup[$data['id']] = $data;
		}
		return $backup;
	}
	/**
	 * Récupère la dernière sauvegarde, et écrase le fichier en cours.
	 */
	public function loadBackup() {
		$this->getBackup();
		copy($this->jsonFileBack, $this->jsonFile);
	}

	/**
	 * Insère une ligne dans les datas du Json
	 * @param ARRAY $entry Un tableau associatif correspondant à l'entrée à ajouter
	 * @param BOOLEAN $autoDate TRUE pour ajouter automatiquement la date de création si la colonne 'DATE_CREATION' (cf. config) existe (default TRUE)
	 * @param BOOLEAN $allowAddRow TRUE pour autoriser l'ajout de colonnes qui ne sont pas dans la structure de la table. FALSE pour les ignorer (default FALSE)
	 * @param BOOLEAN $checkMissing TRUE pour vérifier qu'il ne manque pas de colonne (renvoie une erreur). FALSE pour ignorer (default TRUE)
	 */
	public function insertEntry($entry, $autoDate=true, $allowAddRow=false, $checkMissing=true, $checkType=true) {
		if (!is_array($entry))
			throw new Exception("JsonDB::insertEntry() : Il manque les données (ou ce n'est pas un tableau associatif)");
		if (!is_array($this->jtableData))
			throw new Exception("JsonDB::insertEntry() : Données de la table corrompues ('jTableData' not an array)");
		if ($autoDate && defined("DATE_CREATION") && DATE_CREATION !== false) {
			if (array_key_exists(DATE_CREATION, $this->jtableStruct))
				$entry[DATE_CREATION] = date('Y-m-d H:i:s');
		}
		$entry['id'] = $this->nextID;
		if ($checkMissing){
			$missingRows = array_diff_key($this->jtableStruct, $entry);
			if (count($missingRows) > 0)
				throw new Exception("JsonDB::insertEntry() : Pour la table '$this->table', il manque les colonnes suivantes : ".json_encode($missingRows));
		}
		if (!$allowAddRow) {
			$surplusRows = array_diff_key($entry, $this->jtableStruct);
			if (count($surplusRows) > 0) {
				foreach($surplusRows as $sRow=>$sVal)
					unset($entry[$sRow]);
			}
		}
		if ($checkType) {
			foreach($entry as $col => $val) {
				$type = gettype($val);
				$typeOK = $this->jtableStruct[$col];
				if ($typeOK == 'date')
					$typeOK = 'string';
				if (preg_match('/:/', $typeOK))
					$typeOK = 'integer';
				if ($type !== $typeOK)
					throw new Exception("JsonDB::insertEntry() : La valeur du champ '$col' doit être du type '$typeOK' (et non '$type')");
			}
		}
		$this->jtableData[] = $entry;
		$this->nextID += 1;
	}

	/**
	 * Modifie une entrée dans la table Json
	 * @param INT $id Le numéro d'ID de l'entrée à modifier
	 * @param ARRAY $newVals Un tableau associatif avec les nouvelles valeurs pour l'entrée (toutes, ou seulement une partie)
	 * @param BOOLEAN $silentClean FALSE pour envoyer une erreur si des colonnes en trop dans $newVals sont trouvées. Sinon (TRUE), suppression silencieuse des données en trop (default TRUE)
	 */
	public function modifyEntry($id, $newVals, $silentClean=true) {
		if (!is_array($this->jtableData))
			throw new Exception("JsonDB::modifyEntry() : Données de la table corrompues ('jTableData' not an array)");
		if (!is_array($newVals))
			throw new Exception("JsonDB::modifyEntry() : Les nouvelles valeurs doivent être un tableau (array)");
		$surplusRows = array_diff_key($newVals, $this->jtableStruct);
		if (count($surplusRows) > 0) {
			if (!$silentClean)
				throw new Exception("JsonDB::modifyEntry() : Les colonnes ".json_encode(array_keys($surplusRows))." n'existent pas dans la table '$this->table'");
			foreach($surplusRows as $sRow=>$sVal)
				unset($newVals[$sRow]);
		}
		unset($newVals['id']);
		$entryKey = array_search((int)$id, array_column($this->jtableData, 'id'));
		if ($entryKey === false)
			throw new Exception("JsonDB::modifyEntry() : Entrée introuvable pour id=$id");
		$this->jtableData[(int)$entryKey] = array_merge($this->jtableData[(int)$entryKey], $newVals);
	}

	/**
	 * Supprime une ligne dans les datas du Json
	 * @param INT $id Le numéro d'ID de la ligne à supprimer
	 */
	public function deleteEntry($id=null) {
		if (!is_array($this->jtableData))
			throw new Exception("JsonDB::deleteEntry() : Données de la table corrompues ('jTableData' not an array)");
		if ($id === null)
			throw new Exception("JsonDB::deleteEntry() : Il manque l'ID de l'entrée à supprimer");
		$entryKey = array_search((int)$id, array_column($this->jtableData, 'id'));
		if ($entryKey !== false)
			unset($this->jtableData[(int)$entryKey]);
	}

	/**
	 * Sauvegarde les data de la table dans le fichier Json
	 */
	public function saveJson() {
		$jsonData = Array(
			"structure" => $this->jtableStruct,
			"data"		=> $this->jtableData,
			"nextID"	=> $this->nextID
		);
		$newJtable = json_encode($jsonData, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_NUMERIC_CHECK);
		if (!$newJtable)
			throw new Exception("JsonDB::saveJson() : Impossible d'encoder les données en Json");
		// Copie pour backup
		if (is_file($this->jsonFile)) {
			$jsonStr   = @file_get_contents($this->jsonFile);
			$jsonCheck = json_decode($jsonStr, true);	// Check que le fichier était bien formatté Json
			if ($jsonCheck != null)
				copy($this->jsonFile, $this->jsonFileBack);
		}
		$fs = @file_put_contents($this->jsonFile, $newJtable);		// Écriture du fichier
		if ($fs === false)
			throw new Exception("JsonDB::saveJson() : Impossible d'écrire dans le fichier $this->jsonFile");
	}


///////////////////////////////////////////////// METHODES PRIVÉES /////////////////////////////////////////////////

	/**
	 * Création d'une table (fichier Json) selon une structure donnée
	 * @param ARRAY $structure Un tableau contenant la liste des colonnes et leur type. Ex : Array("id"=>"int","nom"=>"string"...)
	 */
	private function createTable($structure) {
		if (!is_array($structure))
			throw new Exception("JsonDB::createTable() : Il manque la structure de la table à créer (doit être un tableau)");
		if (file_exists($this->jsonFile))
			throw new Exception("JsonDB::createTable() : Le fichier pour la table '$this->table' existe déjà ! Vérifiez qu'il soit lisible");
		$structure['id'] = 'integer';
		$newTable = Array(
			"structure" => $structure,
			"data"		=> Array(),
			"nextID"	=> 1
		);
		$newJtable = json_encode($newTable, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT+JSON_NUMERIC_CHECK);
		$fs = @file_put_contents($this->jsonFile, $newJtable);		// écriture du fichier
		if ($fs === false)
			throw new Exception("JsonDB::createTable() : Impossible d'écrire dans le fichier $this->jsonFile");
		$this->jtableStruct	= $structure;
		$this->nextID		= 1;
		$this->jtableData = Array();
	}
}

///////////////////////////////////////////////////// FONCTIONS GÉNÉRIQUES (hors classe) /////////////////////////////////////////////////////

/**
 * Fonction de comparaison de texte
 */
function sortDataByText($a, $b) {
    return strcmp($a, $b);
}
/**
 * Fonction de comparaison d'entier
 */
function sortDataByInt($a, $b) {
    if ((int)$a == (int)$b)
        return 0;
    return ((int)$a < (int)$b) ? -1 : 1;
}
/**
 * Fonction de comparaison de nombre flottant
 */
function sortDataByFloat($a, $b) {
    if ((float)$a == (float)$b)
        return 0;
    return ((float)$a < (float)$b) ? -1 : 1;
}
/**
 * Fonction de comparaison de date
 */
function sortDataByDate($a, $b) {
	$Da = new DateTime($a);
	$Db = new DateTime($b);
	if ($Da == $dB)
		return 0;
	return ($Da < $Db) ? -1 : 1;
}