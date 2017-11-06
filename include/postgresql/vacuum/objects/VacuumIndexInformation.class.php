<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
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

class VacuumIndexInformation {
	var $vacuumedTable;
	var $indexName;
	var $numberOfRowVersions = 0;
	var $numberOfPages = 0;
	var $numberOfRemovedRowVersions = 0;
	var $numberOfDeletedPages = 0;
	var $numberOfReusablePages = 0;
	var $systemCpuUsage = 0;
	var $userCpuUsage = 0;
	var $duration = 0;

	function VacuumIndexInformation(& $vacuumedTable, $indexName, $numberOfRowVersions, $numberOfPages) {
		$this->vacuumedTable =& $vacuumedTable;
		$this->indexName = $indexName;
		$this->numberOfRowVersions = $numberOfRowVersions;
		$this->numberOfPages = $numberOfPages;
	}
	
	function setDetailedInformation($numberOfRemovedRowVersions, $numberOfDeletedPages, $numberOfReusablePages,
		$systemCpuUsage, $userCpuUsage, $duration) {
		$this->numberOfRemovedRowVersions = $numberOfRemovedRowVersions;
		$this->numberOfDeletedPages = $numberOfDeletedPages;
		$this->numberOfReusablePages = $numberOfReusablePages;
		$this->systemCpuUsage = $systemCpuUsage;
		$this->userCpuUsage = $userCpuUsage;
		$this->duration = $duration;
		
		if($this->vacuumedTable->hasDuration()) {
			// it's a vacuum full, we add the index rusage to get the global rusage
			$this->vacuumedTable->addSystemCpuUsage($this->getSystemCpuUsage());
			$this->vacuumedTable->addUserCpuUsage($this->getUserCpuUsage());
			$this->vacuumedTable->addDuration($this->getDuration());
		}
	}
	
	function getIndexName() {
		return $this->indexName;
	}
	
	function getNumberOfRowVersions() {
		return $this->numberOfRowVersions;
	}
	
	function getNumberOfPages() {
		return $this->numberOfPages;
	}
	
	function getNumberOfRemovedRowVersions() {
		return $this->numberOfRemovedRowVersions;
	}
	
	function getNumberOfDeletedPages() {
		return $this->numberOfDeletedPages;
	}
	
	function getNumberOfReusablePages() {
		return $this->numberOfReusablePages;
	}
	
	function getDuration() {
		return $this->duration;
	}
	
	function getCpuUsage() {
		return $this->systemCpuUsage + $this->userCpuUsage;
	}
	
	function getSystemCpuUsage() {
		return $this->systemCpuUsage;
	}
	
	function getUserCpuUsage() {
		return $this->userCpuUsage;
	}
}
