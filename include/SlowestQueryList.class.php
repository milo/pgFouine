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

class SlowestQueryList {
	public $size;
	public $queries = [];
	public $queriesCount = 0;
	public $shortestDuration = 100000000;
	
	function SlowestQueryList($size) {
		$this->size = $size;
	}
	
	function setSize($size) {
		$this->size = $size;
	}
	
	function addQuery(&$query) {
		$duration = (string) $query->getDuration();
		$queriesCount = $this->queriesCount;
		$shortestDuration = (string) $this->shortestDuration;
		
		if($queriesCount < $this->size) {
			if(!array_key_exists($duration, $this->queries)) {
				$this->queries[$duration] = [];
			}
			$this->queries[$duration][] =& $query;
			$this->shortestDuration = min($shortestDuration, $duration);
			$this->queriesCount++;
		} else {
			if($shortestDuration < $duration) {
				$shortestDurationQueriesCount = count($this->queries[$shortestDuration]);
				if($shortestDurationQueriesCount == 1) {
					unset($this->queries[$shortestDuration]);
				} else {
					unset($this->queries[$shortestDuration][$shortestDurationQueriesCount - 1]);
				}
				if(!array_key_exists($duration, $this->queries)) {
					$this->queries[$duration] = [];
				}
				$this->queries[$duration][] =& $query;
				$this->shortestDuration = min(array_keys($this->queries));
			}
		}
	}
	
	function & getQueries() {
		return $this->queries;
	}
	
	function & getSortedQueries() {
		$queryList = [];
		krsort($this->queries, SORT_NUMERIC);
		$keys = array_keys($this->queries);
		foreach($keys AS $key) {
			$queryArrayCount = count($this->queries[$key]);
			for($i = 0; $i < $queryArrayCount; $i++) {
				$queryList[] =& $this->queries[$key][$i];
			}
		}
		return $queryList;
	}
	
	function & getLastQuery() {
		$queryList =& last($this->queries);
		return last($queryList);
	}
}
