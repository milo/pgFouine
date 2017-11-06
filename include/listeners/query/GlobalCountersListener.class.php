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

class GlobalCountersListener extends QueryListener {
	var $counter;
	var $firstQueryTimestamp = MAX_TIMESTAMP;
	var $lastQueryTimestamp = MIN_TIMESTAMP;
	
	var $queryPeakByTimestamp = array();
	
	function GlobalCountersListener() {
		$this->counter = new QueryCounter();
	}
	
	function fireEvent(& $logObject) {
		$objectTimestamp = $logObject->getTimestamp();
		
		$this->firstQueryTimestamp = min($objectTimestamp, $this->firstQueryTimestamp);
		$this->lastQueryTimestamp = max($objectTimestamp, $this->lastQueryTimestamp);
		
		$this->counter->incrementQuery($logObject->getDuration());
		
		if($logObject->getEventType() == EVENT_QUERY) {
			$this->counter->incrementIdentifiedQuery($logObject->getDuration());
			if($logObject->isSelect()) {
				$this->counter->incrementSelect($logObject->getDuration());
			} elseif($logObject->isUpdate()) {
				$this->counter->incrementUpdate($logObject->getDuration());
			} elseif($logObject->isInsert()) {
				$this->counter->incrementInsert($logObject->getDuration());
			} elseif($logObject->isDelete()) {
				$this->counter->incrementDelete($logObject->getDuration());
			}
		}
		
		if(!isset($this->queryPeakByTimestamp[$objectTimestamp])) {
			$this->queryPeakByTimestamp[$objectTimestamp] = 0;
		}
		$this->queryPeakByTimestamp[$objectTimestamp] ++;
	}
	
	function getSubscriptions() {
		return array_merge(parent::getSubscriptions(), array(EVENT_DURATION_ONLY));
	}
	
	function getQueryCount() {
		return $this->counter->getQueryCount();
	}
	
	function getQueryDuration() {
		return $this->counter->getQueryDuration();
	}
	
	function getIdentifiedQueryCount() {
		return $this->counter->getIdentifiedQueryCount();
	}
	
	function getIdentifiedQueryDuration() {
		return $this->counter->getIdentifiedQueryDuration();
	}
	
	function getSelectCount() {
		return $this->counter->getSelectCount();
	}
	
	function getUpdateCount() {
		return $this->counter->getUpdateCount();
	}
	
	function getInsertCount() {
		return $this->counter->getInsertCount();
	}
	
	function getDeleteCount() {
		return $this->counter->getDeleteCount();
	}
	
	function getFirstQueryTimestamp() {
		return $this->firstQueryTimestamp;
	}
	
	function getLastQueryTimestamp() {
		return $this->lastQueryTimestamp;
	}
	
	function getQueryPeakTimestamps() {
		$peakTimestamps = false;
		if(!empty($this->queryPeakByTimestamp)) {
			$peakTimestamps = array_keys($this->queryPeakByTimestamp, max($this->queryPeakByTimestamp));
		}
		return $peakTimestamps;
	}
	
	function getQueryPeakQueryCount() {
		$peakQueryCount = false;
		if(!empty($this->queryPeakByTimestamp)) {
			$peakQueryCount = max($this->queryPeakByTimestamp);
		}
		return $peakQueryCount;
	}
}
