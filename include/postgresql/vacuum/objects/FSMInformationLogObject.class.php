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

class FSMInformationLogObject extends VacuumLogObject {
	public $currentNumberOfPageSlots = 0;
	public $currentNumberOfRelations = 0;
	
	public $pageSlotsInUse = 0;
	
	public $pageSlotsRequired = 0;
	
	public $maxNumberOfPageSlots = 0;
	public $maxNumberOfRelations = 0;
	public $size = 0;
	
	function __construct($currentNumberOfPageSlots, $currentNumberOfRelations) {
		$this->currentNumberOfPageSlots = $currentNumberOfPageSlots;
		$this->currentNumberOfRelations = $currentNumberOfRelations;
	}
	
	function getEventType() {
		return EVENT_FSM_INFORMATION;
	}
	
	function setDetailedInformation($pageSlotsInUse,
		$pageSlotsRequired,
		$maxNumberOfPageSlots, $maxNumberOfRelations, $size
	) {
		$this->pageSlotsInUse = $pageSlotsInUse;
		$this->pageSlotsRequired = $pageSlotsRequired;
		$this->maxNumberOfPageSlots = $maxNumberOfPageSlots;
		$this->maxNumberOfRelations = $maxNumberOfRelations;
		$this->size = $size;
	}
	
	function getcurrentNumberOfPageSlots() {
		return $this->currentNumberOfPageSlots;
	}
	
	function getCurrentNumberOfRelations() {
		return $this->currentNumberOfRelations;
	}
	
	function getPageSlotsInUse() {
		return $this->pageSlotsInUse;
	}
	
	function getPageSlotsRequired() {
		return $this->pageSlotsRequired;
	}
	
	function getMaxNumberOfPageSlots() {
		return $this->maxNumberOfPageSlots;
	}
	
	function getMaxNumberOfRelations() {
		return $this->maxNumberOfRelations;
	}
	
	function getSize() {
		return $this->size;
	}
}
