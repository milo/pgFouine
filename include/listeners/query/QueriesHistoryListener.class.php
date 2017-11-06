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

class QueriesHistoryListener extends QueryListener {
	public $queries = [];
	public $counter = 0;
	
	function fireEvent(& $logObject) {
		$this->counter ++;
		$logObject->setNumber($this->counter);
		$this->queries[] =& $logObject;
	}
	
	function & getQueriesHistory() {
		usort($this->queries, [$this, 'compareTimestamp']);
		return $this->queries;
	}
	
	function & getQueriesHistoryPerConnection() {
		usort($this->queries, [$this, 'compareConnectionId']);
		return $this->queries;
	}
	
	function compareConnectionId(& $a, & $b) {
		if($a->getConnectionId() == $b->getConnectionId()) {
			return $this->compareTimestamp($a, $b);
		} elseif($a->getConnectionId() < $b->getConnectionId()) {
			return -1;
		} else {
			return 1;
		}
	}
	
	function compareTimestamp(& $a, & $b) {
		if($a->getTimestamp() == $b->getTimestamp()) {
			return $this->compareNumber($a, $b);
		} elseif($a->getTimestamp() < $b->getTimestamp()) {
			return -1;
		} else {
			return 1;
		}
	}
	
	function compareNumber(& $a, & $b) {
		if($a->getNumber() == $b->getNumber()) {
			return 0;
		} elseif($a->getNumber() < $b->getNumber()) {
			return -1;
		} else {
			return 1;
		}
	}
}
