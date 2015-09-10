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
 * Bug object, to add a bug or change informations of a bug
 *
 */
class Bug {

	private $bugInfos;
	private $bugID = false;
	private $bugData = Array();

	/**
	 * BUG object
	 * @param INT|BOOL $bugID The bug ID to load from DB, or FALSE to create a new bug (default FALSE)
	 */
	public function __construct($bugID=false) {
		$this->bugInfos = new Infos('t_bugs');
		if (is_int($bugID)) {
			$this->bugID = $bugID;
			$this->bugInfos->loadInfos('id', $bugID, false, false, false);
			if (!$this->bugInfos->isLoaded())
				throw new Exception("Bug::__construct() : Unable to found bug #$bugID in database.");
			$this->bugData = $this->bugInfos->getInfos();
		}
		else
			$this->initNewBug();
	}
	/**
	 * Retreive the bug's informations from DB
	 * @param BOOLEAN $withParsing TRUE to retreive foreign keys, json as arrays, and dates as ISO 8601
	 * @return ARRAY Bug informations
	 */
	public function getBugData($withParsing=true) {
		if ($withParsing)
			$this->bugInfos->loadInfos('id', $this->bugID);
		return $this->bugInfos->getInfos();
	}
	/**
	 * Check if a bug is closed
	 * @return BOOLEAN True if bug is closed
	 */
	public function is_closed() {
		if ($this->bugData['closed'] === 1)
			return true;
		return false;
	}
	/**
	 * Gives a set of informations for the bug
	 * @param ARRAY $vals some informations to set for the bug
	 */
	public function setBugData($vals) {
		if (!is_array($vals))
			throw new Exception("Bug::setBugData() : Bug values must be an array!");
		foreach($vals as $col=>$val)
			$this->bugData[$col] = $val;
		$this->bugInfos->setAllInfos($this->bugData);
	}
	/**
	 * Set the bug as closed
	 */
	public function killBug() {
		$this->bugData['closed'] = 1;
		$this->bugInfos->setInfo('closed', 1);
		$this->save();
	}
	/**
	 * Save new bug's informations
	 */
	public function save() {
		$this->bugInfos->save();
	}
	/**
	 * Delete a bug in database, and its associated comments
	 */
	public function removeBug(){
		foreach(json_decode($this->bugData['FK_comment_ID']) as $commID) {
			$iC = new Infos('t_comments');
			$iC->loadInfos('id', $commID);
			$iC->delete();
		}
		$this->bugInfos->delete();
	}
	/**
	 * Add a new comment to the bug
	 * @param STRING $text Comment message
	 */
	public function addComment($text) {
		if (strlen($text) < 3)
			throw new Exception("Bug::addComment() : comment text is too short!");
		$iC = new Infos('t_comments');
		$commInfos = Array(
			'date' => date('Y-m-d H:i:s'),
			'message' => $text,
			'FK_dev_ID' => -1,
			'FK_bug_ID' => (int)$this->bugData['id']
		);
		$iC->setAllInfos($commInfos);
		$iC->save('id', 'this', false, false);
		$newCommID = $iC->getInfos('id');
		$commList = json_decode($this->bugData['FK_comment_ID']);
		$commList[] = $newCommID;
		$this->bugData['FK_comment_ID'] = json_encode($commList);
		$this->bugInfos->setInfo('FK_comment_ID', $this->bugData['FK_comment_ID']);
		$this->save();
		$iC->loadInfos('id', $newCommID);
		return $iC->getInfos();
	}
	/**
	 * Update a comment's text
	 * @param INT $idComm Comment ID
	 * @param STRING $text Comment message
	 */
	public function updateComment($idComm, $text) {
		if (!is_int($idComm))
			throw new Exception("Bug::updateComment() : comment ID not a integer!");
		if (strlen($text) < 3)
			throw new Exception("Bug::updateComment() : comment text is too short!");
		$iC = new Infos('t_comments');
		$iC->loadInfos('id', $idComm, false, false, false);
		$iC->setInfo('message', $text);
		$iC->save('id', 'this', false, false);
	}
	/**
	 * Delete a comment and remove it from bug's comment list
	 * @param INT $idComm Comment ID
	 */
	public function deleteComment($idComm) {
		if (!is_int($idComm))
			throw new Exception("Bug::updateComment() : comment ID not a integer!");
		$iC = new Infos('t_comments');
		$iC->loadInfos('id', $idComm);
		$iC->delete();
		$commList = json_decode($this->bugData['FK_comment_ID']);
		$commListOK = Array();
		foreach($commList as $comm) {
			if ($comm == $idComm) continue;
			$commListOK[] = $comm;
		}
		$this->bugData['FK_comment_ID'] = json_encode($commListOK);
		$this->bugInfos->setInfo('FK_comment_ID', $this->bugData['FK_comment_ID']);
		$this->save();
	}

	/***************************************************************************/

	/**
	 * Populates a new bug's data with all table's columns with defaults
	 */
	private function initNewBug() {
		$this->bugID = Liste::getAIval($this->bugInfos->getTable());
		foreach(Liste::getCols($this->bugInfos->getTable()) as $col) {
			$val = '';
			if ($col === 'id')		 $val = $this->bugID;
			if ($col === 'date')	 $val = date('Y-m-d H:i:s');
			if ($col === 'author')	 $val = 'Admin';
			if ($col === 'priority') $val = 4;
			if ($col === 'closed')	 $val = 0;
			if ($col === 'FK_comment_ID') $val = '[]';
			$this->bugData[$col] = $val;
		}
	}

}
