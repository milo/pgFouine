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

class NormalizedQuery {
	public $normalizedText;
	public $duration = 0;
	public $count = 0;
	public $examples = false;
	public $hourlyStatistics = array();
	
	function NormalizedQuery(& $query) {
		$this->normalizedText = $query->getNormalizedText();
		$maxExamples = CONFIG_MAX_NUMBER_OF_EXAMPLES;
		if($maxExamples) {
			$this->examples = new SlowestQueryList($maxExamples);
		}
		
		$this->addQuery($query);
	}
	
	function addQuery(& $query) {
		$this->count ++;
		$this->duration += $query->getDuration();
		
		$formattedTimestamp = date('Y-m-d H:00:00', $query->getTimestamp());
		if(!isset($this->hourlyStatistics[$formattedTimestamp])) {
			$this->hourlyStatistics[$formattedTimestamp]['count'] = 0;
			$this->hourlyStatistics[$formattedTimestamp]['duration'] = 0;
		}
		$this->hourlyStatistics[$formattedTimestamp]['count']++;
		$this->hourlyStatistics[$formattedTimestamp]['duration']+= $query->getDuration();
		
		if($this->examples) {
			$this->examples->addQuery($query);
		}
	}
	
	function & getQuery() {
		return $this->examples->getLastQuery();
	}
	
	function getNormalizedText() {
		return $this->normalizedText;
	}
	
	function getTotalDuration() {
		return $this->duration;
	}
	
	function getTimesExecuted() {
		return $this->count;
	}
	
	function getAverageDuration() {
		$average = 0;
		if($this->count > 0) {
			$average = ($this->duration/$this->count);
		}
		return $average;
	}
	
	function & getFilteredExamplesArray() {
		$returnExamples = false;
		
		$examples =& $this->examples->getSortedQueries();
		$exampleCount = count($examples);
		for($i = 0; $i < $exampleCount; $i++) {
			$example =& $examples[$i];
			if($example->getText() != $this->getNormalizedText()) {
				return $examples;
			}
			unset($example);
		}
		$examples = array();
		return $examples;
	}
	
	function & getHourlyStatistics() {
		ksort($this->hourlyStatistics);
		return $this->hourlyStatistics;
	}
}
