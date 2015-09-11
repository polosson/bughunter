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
 * SQL TABLE LISTING abstraction layer, with advanced filters
 */
class Liste {
	/**
	 * @var STRING Résultat de la chaine compilée par getListe() au moment de la requête SQL
	 */
	public $request;
	/**
	 * @var ARRAY Résultat des entrées retournées par getListe()
	 */
	public $listResult;
	/**
	 * @var OBJECT Instance PDO pour la connexion SQL
	 */
	protected $bddCx;
	/**
	 * @var STRING Nom de la table courante
	 */
	protected $table;

	private $what;
	private $tri;
	private $ordre;
	private $filtre_key;
	private $filtre;
	private $lastLogiquefiltre;
	private $isFiltred = false;
	private $filtres = Array();
	private $filtreSQL;

	/**
	 * LISTING de table SQL
	 * @param OBJECT $pdoInstance Une instance de PDO préinitialisée (optionnel)
	 */
	public function __construct ($pdoInstance=false) {
		if (!$pdoInstance || !is_object($pdoInstance))
			$this->initPDO();
		else
			$this->bddCx = $pdoInstance;
	}
	public function __destruct () {
		$this->bddCx = null;
	}

	/**
	 * Initialise une liste de données à récupérer pour une table donnée
	 * @param STRING $table Le nom de la table
	 * @param STRING $want Une liste de colonnes à retourner (default '*' (tout))
	 * @param STRING $tri La colonne à utiliser pour le tri (default 'id')
	 * @param STRING $ordre La direction du tri (default 'ASC')
	 * @param STRING $filtre_key La colonne à utiliser pour filtrer les résultats (default FALSE (pas de filtre))
	 * @param STRING $filtre_comp La comparaison à effectuer pour le filtrage (default '=')
	 * @param STRING $filtre La valeur à utiliser pour filtrer les résultats (default null)
	 * @param INT $limit Nombre maximum de données à retourner (default FALSE (pas de limite)
	 * @param BOOLEAN $withFK TRUE pour récupérer les données JOINTES (cf config->table relations) (default TRUE)
	 * @param BOOLEAN $decodeJson TRUE pour décoder les champs contenant du JSON automatiquement. FALSE pour avoir les champs JSON au format STRING (default TRUE)
	 * @param BOOLEAN $parseDatesJS TRUE pour formater les dates au format ISO 8601 pour javascript (default TRUE)
	 * @return ARRAY Le tableau des résultats, ou FALSE si aucune donnée
	 */
	public function getListe ($table, $want='*', $tri='id', $ordre='ASC', $filtre_key=false, $filtre_comp='=', $filtre=null, $limit=false, $withFK=true, $decodeJson=true, $parseDatesJS=true) {
		global $DATE_FIELDS;
		$this->listResult = Array();
		$this->table = $table;
		$this->what  = $want;
		$this->tri	 = $tri;
		$this->ordre = $ordre;
		// Check si table existe
		if (!$this->check_table_exist($table))
			throw new Exception("Liste::getListe() : La table '$table' n'existe pas");
		// pour chaque filtre défini par Liste::addFiltre()
		if (is_array($this->filtres) && count($this->filtres) > 0) {
			$FM = '' ;
			foreach ($this->filtres as $f) $FM .= $f;
			$filtrage_multiple = trim($FM, " $this->lastLogiquefiltre ");
		}
		if ($filtre_key && (string)$filtre != null ) {
			if (Liste::check_col_exist($filtre_key)) {
				$this->isFiltred  = true;
				$this->filtre_key = $filtre_key;
				$this->filtre	  = addslashes($filtre);
			}
			else return false ;
		}
		if ($this->isFiltred)
			$this->request = "SELECT $this->what FROM `$this->table` WHERE `$this->filtre_key` $filtre_comp '$this->filtre' ORDER BY `$tri` $ordre";
		elseif (isset($filtrage_multiple))
			$this->request = "SELECT $this->what FROM `$this->table` WHERE $filtrage_multiple ORDER BY `$tri` $ordre";
		elseif (isset($this->filtreSQL))
			$this->request = "SELECT $this->what FROM `$this->table` WHERE $this->filtreSQL ORDER BY `$tri` $ordre";
		else
			$this->request = "SELECT $this->what FROM `$this->table` ORDER BY `$this->tri` $this->ordre";
		if (is_int($limit))
			$this->request .= " LIMIT $limit";
		$q = $this->bddCx->prepare($this->request) ;
		$q->execute();

		if ($q->rowCount() >= 1) {							// Formatage des résultats de la requête
			$result = $q->fetchAll(PDO::FETCH_ASSOC);
			$retour = array();
			$i = 0;
			foreach ($result as $resultOK) {
				unset($resultOK['password']);
				foreach ($resultOK as $k => $v) {					// Décodage JSON le cas échéant
					if ($decodeJson && is_string($v) && preg_match('/((^\[)*(]$))|((^\{")*(}$))/', $v)) {
						$valArr = json_decode($v, true);
						if (!is_array($valArr)) continue;
						$resultOK[$k] = $valArr;
					}												// Remplacement par la Foreign Key le cas échéant
					if ($withFK && preg_match("/^".FOREIGNKEYS_PREFIX."/", $k)) {
						$fk = $this->getForeignKey($k, $v, $decodeJson, $parseDatesJS);
						if (!is_array($fk)) continue;
						$resultOK[$fk[0]] = $fk[1];
					}
					if (is_array(@$DATE_FIELDS) && $parseDatesJS) {
						if (in_array($k, $DATE_FIELDS))
							$resultOK[$k] = date("c", strtotime($v));	// Formatage de la date au format ISO 8601 (pour que JS puisse la parser)
					}
				}
				if (count($resultOK) == 1)							// Si une seule valeur demandée, pas besoin d'une dimension en plus
					$retour[$i] = reset($resultOK);
				else
					$retour[$i] = $resultOK;
				$i++;
			}
			$this->listResult = $retour ;
			return $retour;
		}
		else return false;
	}
	/**
	 * Retourne le nombre d'entrées trouvées
	 */
	public function countResults () {
		if (!$this->listResult)
			return 0;
		return count($this->listResult);
	}

	/**
	 * Ajoute une condition au filtre pour la requête
	 * @param STRING $filtre_key Le nom du champ pour le filtre
	 * @param STRING $filtre_comp La comparaison à utiliser pour le filtre (default "=")
	 * @param STRING $filtre La valeur à comparer
	 * @param STRING $logique Le type de logique à utiliser avec les éventuels précédents filtres (default "AND")
	 */
	public function addFiltre($filtre_key=false, $filtre_comp='=', $filtre=false , $logique='AND'){
		if (!$filtre_key) throw new Exception("Il manque le nom du champ à utiliser pour le filtre");
		$filtre = addslashes($filtre);
		$this->filtres[] = " (`$filtre_key` $filtre_comp '$filtre') $logique " ;
		$this->lastLogiquefiltre = $logique;
	}
	/**
	 * Ajoute une condition au filtre pour la requête, en mode "moins sécurisé", afin de permettre les fonctions SQL à la place d'une string pour $filtre.
	 * @param STRING $filtre_key Le nom du champ pour le filtre
	 * @param STRING $filtre_comp La comparaison à utiliser pour le filtre (default "=")
	 * @param STRING $filtre La valeur à comparer
	 * @param STRING $logique Le type de logique à utiliser avec les éventuels précédents filtres (default "AND")
	 */
	public function addFiltreRaw($filtre_key=false, $filtre_comp='=', $filtre=false , $logique='AND'){
		if (!$filtre_key) throw new Exception("Il manque le nom du champ à utiliser pour le filtre");
		$filtre = addslashes($filtre);
		$this->filtres[] = " (`$filtre_key` $filtre_comp $filtre) $logique " ;
		$this->lastLogiquefiltre = $logique;
	}
	/**
	 * Réinitialise le filtrage (pour effectuer une nouvelle requête, par ex.)
	 */
	public function resetFiltre() {
		$this->isFiltred  = false;
		$this->filtre_key = false;
		$this->filtre	  = null;
		$this->filtres	  = null;
		$this->lastLogiquefiltre = null;
	}
	/**
	 * Défini un filtre manuel en SQL
	 * @param STRING $filtre Le filtre SQL (ex. "`id` >= 30 AND `date` <= NOW()")
	 */
	public function setFiltreSQL( $filtre ){
		$this->filtreSQL = $filtre ;
	}


	/**
	 * Renvoie un tableau où l'index est $wantedInd au lieu de 0,1,2,3,...
	 * @param STRING $wantedInd Le nom du champ à utiliser comme index
	 * @return ARRAY Le nouveau tableau avec l'index remplacé, FALSE si erreur
	 */
	public function simplifyList ($wantedInd=null) {
		if ($this->listResult == null || empty ($this->listResult)) return false ;
		if ($wantedInd == null) $wantedInd = 'id' ;
		$newTableau = array();
		foreach ($this->listResult as $entry) {
			$ind = $entry[$wantedInd];
			$newTableau[$ind] = $entry ;
		}
		return $newTableau ;
	}

	///////////////////////////////////////////////////////////// METHODES PRIVÉES /////////////////////////////////////////////////////

	/**
	 * Initialisation de l'objet PDO si pas encore en mémoire
	 */
	protected function initPDO () {
		$this->bddCx = null;
		$this->bddCx = new PDO(DSN, USER, PASS, array(PDO::ATTR_PERSISTENT => false));
		$this->bddCx->query("SET NAMES 'utf8'");
		$this->bddCx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	/**
	 * Vérifie si une table existe dans la base de données
	 * @param STRING $table Le nom de la table
	 * @return BOOLEAN True si la table existe
	 */
	protected function check_table_exist ($table) {
		$q = $this->bddCx->prepare("SHOW TABLES LIKE '$table'");
		$q->execute();
		if ($q->rowCount() >= 1)
			return true;
		else return false;
	}
	/**
	 * Vérifie si un champ existe dans la table actuelle
	 * @param STRING $champ Le nom du champ
	 * @return BOOLEAN
	 */
	protected function check_col_exist ($champ) {
		$q = $this->bddCx->prepare("SELECT `$champ` FROM `$this->table`");
		$q->execute();
		return ($q->rowCount() >= 1);
	}

	/**
	 *
	 * @param STRING $k Le nom de la clé dont on veut la jointure (origine)
	 * @param INT $v La valeur à rechercher (ID de la destination)
	 * @param BOOLEAN $decodeJson TRUE pour décoder les champs contenant du JSON automatiquement. FALSE pour avoir les champs JSON au format STRING (default TRUE)
	 * @param BOOLEAN $parseDatesJS TRUE pour formater les dates au format ISO 8601 pour javascript (default TRUE)
	 * @return ARRAY Une paire (clé, valeur) de la jointure trouvée, FALSE si aucune jointure trouvée
	 */
	protected function getForeignKey ($k, $v, $decodeJson=true, $parseDatesJS=true) {
		global $RELATIONS;
		global $DATE_FIELDS;
		if (!isset($RELATIONS))
			return false;
		if ($v == "")
			return false;
		$rel = $RELATIONS[$k];
		$sqlReq = "SELECT * FROM `".$rel['table']."` WHERE";
		$vArr = json_decode($v);
		if (is_array($vArr)) {
			if (count($vArr) > 0) {
				foreach($vArr as $val)
					$sqlReq .= " `id` = $val OR";
				$sqlReq = trim($sqlReq, ' OR');
			}
			else
				return false;
		}
		else
			$sqlReq .= " `id` = $v";
		$q = $this->bddCx->prepare($sqlReq) ;
		$q->execute();
		$nbResults = $q->rowCount();
		if ($nbResults == 0)
			return false;
		if (is_array($vArr) && count($vArr) > 0)
			$retour = $q->fetchAll(PDO::FETCH_ASSOC);
		else
			$retour = $q->fetch(PDO::FETCH_ASSOC);
		$resultOK = $retour;
		foreach($retour as $i => $entry) {
			if (!is_array($entry)) continue;
			foreach($entry as $kb => $vb) {
				if ($decodeJson && is_string($vb) && preg_match('/((^\[)*(]$))|((^\{")*(}$))/', $vb)) {
					$valArr = json_decode($vb, true);
					if (!is_array($valArr)) continue;
					$resultOK[$i][$kb] = $valArr;
				}
				if (preg_match("/^".FOREIGNKEYS_PREFIX."/", $kb)) {
					$fkb = $this->getForeignKey($kb, $vb);
					if (!is_array($fkb)) continue;
					$resultOK[$i][$fkb[0]] = $fkb[1];
				}
				if (is_array(@$DATE_FIELDS) && $parseDatesJS) {
					if (in_array($kb, @$DATE_FIELDS))
						$resultOK[$i][$kb] = date("c", strtotime($vb));
				}
			}
		}
		return Array($rel['alias'], $resultOK);
	}

	///////////////////////////////////////////////////////////// METHODES STATIQUES /////////////////////////////////////////////////////

	/**
	 * Retourne un tableau contenant les noms des champs d'une table
	 * @param STRING $table Le nom de la table
	 * @return ARRAY Le tableau décrivant les champs de la table, FALSE si erreur
	 */
	public static function getCols ($table=false) {
		if (!$table) return false;
		global $bdd;
		$q = $bdd->prepare("DESCRIBE `".$table."`");
		$q->execute();
		return $q->fetchAll(PDO::FETCH_COLUMN);
	}

	/**
	 * Fonction utilitaire statique pour récupérer la valeur maxi d'un champ
	 * @param STRING $table Le nom de la table
	 * @param STRING $champ Le nom du champ
	 * @return MIXED La valeur la plus grande (string la + longue, int le + grand, date la plus récente...) ou FALSE si aucun résultat.
	 */
	public static function getMax ($table, $champ){
		global $bdd;
		$q = $bdd->prepare("SELECT `$champ` from `$table` WHERE `$champ` = (SELECT MAX($champ) FROM `$table`)");
		$q->execute();
		if ($q->rowCount() >= 1) {
			$result = $q->fetch(PDO::FETCH_ASSOC);
			return $result[$champ];
		}
		else return false;

	}

	/**
	 * Retourne la valeur du prochain auto-incrément
	 * @param STRING $table Le nom de la table
	 * @return INT La valeur du prochain auto-incrément
	 */
	public static function getAIval ($table=false) {
		if (!$table) return false;
		global $bdd;
		$q = $bdd->prepare("SHOW TABLE STATUS LIKE '$table'");
		$q->execute();
		if ($q->rowCount() >= 1) {
			$result = $q->fetch(PDO::FETCH_ASSOC);
			$AIval = $result['Auto_increment'];
			return (int)$AIval;
		}
		else return false;
	}

	/**
	 * Fonction utilitaire statique pour retrier un tableau non associatif en un tableau associatif par l'id (1 seule dimension, 1 seule valeur)
	 * @param ARRAY $arr Le tableau à re-trier
	 * @param STRING $champ Le champ à utiliser pour les valeurs du tableau
	 * @return ARRAY Le tableau retrié par ID, ou FALSE si erreur
	 */
	public static function resortById ($arr, $champ='label') {					// @TODO : amélioration du re-triage pour pouvoir mettre plusieurs valeurs
		if (!is_array($arr)) return false;
		$arrOK = array();
		foreach ($arr as $item) {
			if (!isset($item['id'])) return false;
			$arrOK[$item['id']] = $item[$champ];
		}
		return $arrOK;
	}
}