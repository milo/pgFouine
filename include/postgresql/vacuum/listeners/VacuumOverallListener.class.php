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

class VacuumOverallListener {
	var $statistics = array();
	var $statisticsPerDatabase = array();

	function VacuumOverallListener() {
		$this->statistics['numberOfTables'] = 0;
		$this->statistics['numberOfPages'] = 0;
		$this->statistics['numberOfPagesRemoved'] = 0;
		$this->statistics['numberOfRowVersions'] = 0;
		$this->statistics['numberOfRemovableRowVersions'] = 0;
		$this->statistics['duration'] = 0;
		$this->statistics['cpuUsage'] = 0;
	}
	
	function fireEvent(& $vacuumedTable) {
		$this->statistics['numberOfTables'] ++;
		$this->statistics['numberOfPages'] += $vacuumedTable->getNumberOfPages();
		$this->statistics['numberOfPagesRemoved'] += $vacuumedTable->getNumberOfPagesRemoved();
		$this->statistics['numberOfRowVersions'] += $vacuumedTable->getTotalNumberOfRows();
		$this->statistics['numberOfRemovableRowVersions'] += $vacuumedTable->getNumberOfRemovableRows();
		$this->statistics['duration'] += $vacuumedTable->getDuration();
		$this->statistics['cpuUsage'] += $vacuumedTable->getCpuUsage();
		
		if(!isset($this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfTables'])) {
			$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfTables'] = 0;
			$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfPages'] = 0;
			$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfPagesRemoved'] = 0;
			$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfRowVersions'] = 0;
			$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfRemovableRowVersions'] = 0;
			$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['duration'] = 0;
			$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['cpuUsage'] = 0;
		}
		$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfTables'] ++;
		$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfPages'] += $vacuumedTable->getNumberOfPages();
		$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfPagesRemoved'] += $vacuumedTable->getNumberOfPagesRemoved();
		$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfRowVersions'] += $vacuumedTable->getTotalNumberOfRows();
		$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['numberOfRemovableRowVersions'] += $vacuumedTable->getNumberOfRemovableRows();
		$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['duration'] += $vacuumedTable->getDuration();
		$this->statisticsPerDatabase[$vacuumedTable->getDatabase()]['cpuUsage'] += $vacuumedTable->getCpuUsage();
	}
	
	function close() {
	}
	
	function getSubscriptions() {
		return array(EVENT_VACUUM_TABLE);
	}
	
	function getStatisticsPerDatabase() {
		return $this->statisticsPerDatabase;
	}
	
	function getStatistics() {
		return $this->statistics;
	}
}

?>