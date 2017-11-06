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

class VacuumedTablesListener {
	public $vacuumedTables = [];
	public $counter = 1;

	function __construct() {
	}
	
	function fireEvent(& $vacuumedTable) {
		$vacuumedTable->setNumber($this->counter++);
		$this->vacuumedTables[] =& $vacuumedTable;
	}
	
	function close() {
	}
	
	function getSubscriptions() {
		return [EVENT_VACUUM_TABLE/*, EVENT_ANALYZE_TABLE*/];
	}
	
	function & getVacuumedTables() {
		return $this->vacuumedTables;
	}
	
	function & getVacuumedTablesSortedByPercentageOfRowVersionsRemoved() {
		$vacuumedTables = $this->vacuumedTables;
		usort($vacuumedTables, [$this, 'comparePercentageOfRowVersionsRemoved']);
		return $vacuumedTables;
	}
	
	
	function comparePercentageOfRowVersionsRemoved(& $a, & $b) {
		$aPercentage = getExactPercentage($a->getNumberOfRemovableRows(), $a->getTotalNumberOfRows());
		$bPercentage = getExactPercentage($b->getNumberOfRemovableRows(), $b->getTotalNumberOfRows());
	
		if($aPercentage == $bPercentage) {
			return $this->compareNumberOfRowVersionsRemoved($a, $b);
		} elseif($aPercentage < $bPercentage) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function compareNumberOfRowVersionsRemoved(& $a, & $b) {
		if($a->getNumberOfRemovableRows() == $b->getNumberOfRemovableRows()) {
			return 0;
		} elseif($a->getNumberOfRemovableRows() < $b->getNumberOfRemovableRows()) {
			return 1;
		} else {
			return -1;
		}
	}
}
