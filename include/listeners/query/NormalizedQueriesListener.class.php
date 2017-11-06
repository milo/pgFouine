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

class NormalizedQueriesListener extends QueryListener {
	public $queryList = [];
	public $queriesNumber = 10;
	
	function NormalizedQueriesListener() {
		$this->queriesNumber = CONFIG_TOP_QUERIES_NUMBER;
	}
	
	function fireEvent(& $query) {
		$normalizedText = $query->getNormalizedText();
		if(isset($this->queryList[$normalizedText])) {
			$this->queryList[$normalizedText]->addQuery($query);
		} else {
			$this->queryList[$normalizedText] = new NormalizedQuery($query);
		}
	}
	
	function & getQueriesMostTime() {
		$queryList = $this->queryList;
		usort($queryList, [$this, 'compareMostTime']);
		$queries =& array_slice($queryList, 0, $this->queriesNumber);
		return $queries;
	}
	
	function compareMostTime(& $a, & $b) {
		if($a->getTotalDuration() == $b->getTotalDuration()) {
			return 0;
		} elseif($a->getTotalDuration() < $b->getTotalDuration()) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function & getMostFrequentQueries() {
		$queryList = $this->queryList;
		usort($queryList, [$this, 'compareMostFrequent']);
		$queries =& array_slice($queryList, 0, $this->queriesNumber);
		return $queries;
	}
	
	function compareMostFrequent(& $a, & $b, $force = false) {
		if($a->getTimesExecuted() == $b->getTimesExecuted()) {
			if($force) {
				return 0;
			} else {
				return $this->compareSlowest($a, $b, true);
			}
		} elseif($a->getTimesExecuted() < $b->getTimesExecuted()) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function & getSlowestQueries() {
		$queryList = $this->queryList;
		usort($queryList, [$this, 'compareSlowest']);
		$queries =& array_slice($queryList, 0, $this->queriesNumber);
		return $queries;
	}
	
	function compareSlowest(& $a, & $b, $force = false) {
		if($a->getAverageDuration() == $b->getAverageDuration()) {
			if($force) {
				return 0;
			} else {
				return $this->compareMostFrequent($a, $b, true);
			}
		} elseif($a->getAverageDuration() < $b->getAverageDuration()) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function getUniqueQueryCount() {
		return count($this->queryList);
	}
}
