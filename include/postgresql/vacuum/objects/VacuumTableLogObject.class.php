<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2006 Open Wide
 * Copyright (c) 2006-2008 Guillaume Smet
 *
 * pgFouine is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * pgFouine is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with pgFouine; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

class VacuumTableLogObject extends VacuumLogObject {
	var $numberOfRemovableRows = 0;
	var $numberOfNonRemovableRows = 0;
	var $numberOfPages = 0;
	
	var $numberOfNonRemovableDeadRows = 0;
	var $nonRemovableRowMinSize = 0;
	var $nonRemovableRowMaxSize = 0;
	var $numberOfUnusedItemPointers = 0;
	var $totalFreeSpace = 0;
	var $numberOfPagesToEmpty = 0;
	var $numberOfPagesToEmptyAtTheEndOfTheTable = 0;
	var $numberOfPagesWithFreeSpace = 0;
	var $freeSpace = 0;
	
	var $numberOfRowVersionsMoved = 0;
	var $numberOfPagesRemoved = 0;
	
	var $hasDuration = false;
	var $systemCpuUsage = 0;
	var $userCpuUsage = 0;
	var $duration = 0;
	
	var $indexesInformation = array();
	
	var $number;
	
	function VacuumTableLogObject($database, $schema, $table, $ignored = false) {
		$this->VacuumLogObject($database, $schema, $table, $ignored);
	}
	
	function getEventType() {
		return EVENT_VACUUM_TABLE;
	}
	
	function setNumberOfRemovableRows($numberOfRemovableRows) {
		$this->numberOfRemovableRows = $numberOfRemovableRows;
	}
	
	function setNumberOfNonRemovableRows($numberOfNonRemovableRows) {
		$this->numberOfNonRemovableRows = $numberOfNonRemovableRows;
	}
	
	function setNumberOfPages($numberOfPages) {
		$this->numberOfPages = $numberOfPages;
	}
	
	function setNumberOfRowVersionsMoved($numberOfRowVersionsMoved) {
		$this->numberOfRowVersionsMoved = $numberOfRowVersionsMoved;
	}
	
	function setNumberOfPagesRemoved($numberOfPagesRemoved) {
		$this->numberOfPagesRemoved = $numberOfPagesRemoved;
	}
	
	function setDetailedInformation($nonRemovableDeadRows,
		$nonRemovableRowMinSize, $nonRemovableRowMaxSize,
		$unusedItemPointers,
		$totalFreeSpace,
		$numberOfPagesToEmpty, $numberOfPagesToEmptyAtTheEndOfTheTable,
		$numberOfPagesWithFreeSpace, $freeSpace,
		$systemCpuUsage, $userCpuUsage, $duration) {
		
		$this->numberOfNonRemovableDeadRows = $nonRemovableDeadRows;
		$this->nonRemovableRowMinSize = $nonRemovableRowMinSize;
		$this->nonRemovableRowMaxSize = $nonRemovableRowMaxSize;
		$this->numberOfUnusedItemPointers = $unusedItemPointers;
		$this->totalFreeSpace = $totalFreeSpace;
		$this->numberOfPagesToEmpty = $numberOfPagesToEmpty;
		$this->numberOfPagesToEmptyAtTheEndOfTheTable = $numberOfPagesToEmptyAtTheEndOfTheTable;
		$this->numberOfPagesWithFreeSpace = $numberOfPagesWithFreeSpace;
		$this->freeSpace = $freeSpace;

		$this->hasDuration = true;
		$this->systemCpuUsage = $systemCpuUsage;
		$this->userCpuUsage = $userCpuUsage;
		$this->duration = $duration;
	}
	
	function getTablePath() {
		$tablePath = '';
		if($this->database != UNKNOWN_DATABASE) {
			$tablePath .= $this->database.' - ';
		}
		$tablePath .= $this->schema.'.'.$this->table;
		return $tablePath;
	}
	
	function getNumberOfPages() {
		return $this->numberOfPages;
	}
	
	function getNumberOfPagesRemoved() {
		return $this->numberOfPagesRemoved;
	}
	
	function getTotalNumberOfRows() {
		return $this->numberOfRemovableRows + $this->numberOfNonRemovableRows;
	}
	
	function getNumberOfRemovableRows() {
		return $this->numberOfRemovableRows;
	}
	
	function getNumberOfNonRemovableDeadRows() {
		return $this->numberOfNonRemovableDeadRows;
	}
	
	function getNonRemovableRowMinSize() {
		return $this->nonRemovableRowMinSize;
	}
	
	function getNonRemovableRowMaxSize() {
		return $this->nonRemovableRowMaxSize;
	}
	
	function getNumberOfUnusedItemPointers() {
		return $this->numberOfUnusedItemPointers;
	}
	
	function getCpuUsage() {
		return $this->systemCpuUsage + $this->userCpuUsage;
	}
	
	function addSystemCpuUsage($systemCpuUsage) {
		$this->systemCpuUsage += $systemCpuUsage;
	}
	
	function getSystemCpuUsage() {
		return $this->systemCpuUsage;
	}
	
	function addUserCpuUsage($userCpuUsage) {
		$this->userCpuUsage += $userCpuUsage;
	}
	
	function getUserCpuUsage() {
		return $this->userCpuUsage;
	}
	
	function addDuration($duration) {
		$this->duration += $duration;
	}
	
	function getDuration() {
		return $this->duration;
	}
	
	function hasDuration() {
		return $this->hasDuration;
	}
	
	function addIndexInformation(& $indexInformation) {
		$this->indexesInformation[] =& $indexInformation;
	}
	
	function & getLastIndexInformation() {
		return last($this->indexesInformation);
	}
	
	function & getIndexesInformation() {
		return $this->indexesInformation;
	}
	
	function setNumber($number) {
		$this->number = $number;
	}
	
	function getNumber() {
		return $this->number;
	}
	
	function isIgnored() {
		$path = $this->database.'.'.$this->schema.'.'.$this->table;
		
		if(!CONFIG_FILTER || (strpos($path, CONFIG_FILTER) === 0)) {
			$filtered = false;
		} else {
			$filtered = true;
		}
		return ($this->ignored || $filtered);
	}
}

?>