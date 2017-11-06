<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2005-2008 Guillaume Smet
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

class QueryLogObject extends LogObject {
	public $duration = false;
	public $subQueries = [];

	function __construct($connectionId, $user, $db, $text = '', $ignored = false) {
		if(DEBUG > 1 && !$text) stderr('Empty text for QueryLogObject', true);
		
		$this->LogObject($connectionId, $user, $db, $text, $ignored);
	}
	
	function getEventType() {
		return EVENT_QUERY;
	}
	
	function addSubQuery(& $queryLogObject) {
		$this->subQueries[] =& $queryLogObject;
	}
	
	function & getSubQueries() {
		return $this->subQueries;
	}
	
	function appendContext($context) {
		if(!empty($this->subQueries)) {
			$subQuery =& last($this->subQueries);
			$subQuery->setContext($context);
		} else {
			$this->setContext($context);
		}
	}

	function isSelect() {
		return $this->check('select');
	}
	
	function isDelete() {
		return $this->check('delete');
	}
	
	function isInsert() {
		return $this->check('insert');
	}
	
	function isUpdate() {
		return $this->check('update');
	}
	
	function check($start) {
		$queryStart = strtolower(substr(trim($this->text), 0, 10));
		return (strpos($queryStart, $start) === 0);
	}
	
	function isIgnored() {
		return (parent::isIgnored() || (CONFIG_ONLY_SELECT && !$this->isSelect()));
	}
	
	function setDuration($duration) {
		$this->duration = $duration;
	}
	
	function getDuration() {
		return $this->duration;
	}
}
