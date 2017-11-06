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

define('MIN_TIMESTAMP', 0);
define('MAX_TIMESTAMP', 9999999999);

define('EVENT_QUERY', 'query_event_type');
define('EVENT_ERROR', 'error_event_type');
define('EVENT_DURATION_ONLY', 'duration_only_event_type');
define('EVENT_VACUUM_TABLE', 'vacuum_table');
define('EVENT_ANALYZE_TABLE', 'analyze_table');
define('EVENT_FSM_INFORMATION', 'fsm_information');

define('UNKNOWN_DATABASE', 'unknown');

function debug($string, $displayLineNumber = false) {
	stderr($string, $displayLineNumber);
}

function stderr($string, $displayLineNumber = false) {
	global $stderr, $lineParsedCounter;
	if($displayLineNumber && $lineParsedCounter) {
		$string .= ' - log line '.$lineParsedCounter;
	}
	if($stderr) {
		fwrite($stderr, $string."\n");
	}
}

function stderrArray(& $array) {
	$content = getFormattedArray($array);

	stderr($content);
}

function getFormattedArray(& $array) {
	ob_start();
	print_r($array);
	$content = ob_get_contents();
	ob_end_clean();
	
	return $content;
}

function getMemoryUsage() {
	$memoryUsage = memory_get_usage();
	$output = 'Memory usage: ';
	if($memoryUsage < 1024) {
		$output .= intval($memoryUsage).' o';
	} elseif($memoryUsage < 1024*1024) {
		$output .= intval($memoryUsage/1024).' ko';
	} else {
		$output .= number_format(($memoryUsage/(1024*1024)), 2, '.', ' ').' mo';
	}
	return $output;
}

function formatTimestamp($timestamp) {
	return date('Y-m-d H:i:s', $timestamp);
}

function getExactPercentage($number, $total) {
	if($total > 0) {
		$percentage = $number*100/$total;
	} else {
		$percentage = 0;
	}
	return $percentage;
}

function normalizeWhitespaces($text, $keepOnlyIndent = false) {
	if($keepOnlyIndent) {
		$toReplace = '/(?<=[^\s])[ \t]+/m';
	} else {
		$text = trim($text);
		$toReplace = '/\s+/';
	}
	$text = preg_replace($toReplace, ' ', $text);
	return $text;
}

function &last(& $array) {
	if(empty($array)) {
		$last = false;
	} else {
		end($array);
		$last =& $array[key($array)];
	}
	return $last;
}

function &pop(& $array) {
	if(empty($array)) {
		$last = false;
	} else {
		$last =& last($array);
		array_pop($array);
	}
	return $last;
}

function arrayAdd($array1, $array2) {
	$size = count($array1);
	$sum = [];
	for($i = 0; $i < $size; $i++) {
		$sum[] = $array1[$i] + $array2[$i];
	}
	return $sum;
}

function str_putcsv($input, $delimiter = ',', $enclosure = '"') {
	 $fp = fopen('php://temp', 'r+');
	 fputcsv($fp, $input, $delimiter, $enclosure);
	 rewind($fp);
	 $data = fread($fp, 1048576);
	 fclose($fp);
	 
	 return $data;
}

class RegExp {
	public $pattern;
	
	function __construct($pattern) {
		$this->pattern = $pattern;
	}
	
	function & match($text) {
		$found = preg_match($this->pattern, $text, $matches, PREG_OFFSET_CAPTURE);
		$match = false;
		if($found) {
			$match = new RegExpMatch($text, $matches);
		}
		return $match;
	}
	
	function & matchAll($text) {
		$matches = [];
		$found = preg_match_all($this->pattern, $text, $matches, PREG_SET_ORDER);
		
		return $matches;
	}
	
	function replace($text, $replacement) {
		return preg_replace($this->pattern, $replacement, $text);
	}
	
	function getPattern() {
		return $this->pattern;
	}
}

class RegExpMatch {
	public $text;
	public $matches = [];
	
	function __construct($text, & $matches) {
		$this->text = $text;
		$this->matches =& $matches;
	}
	
	function & getMatches() {
		return $this->matches;
	}
	
	function getMatch($position) {
		if(isset($this->matches[$position])) {
			return $this->matches[$position][0];
		} else {
			return false;
		}
	}
	
	function getPostMatch() {
		$postMatch = substr($this->text, $this->matches[0][1] + strlen($this->matches[0][0]));
		return $postMatch;
	}
	
	function getMatchCount() {
		return count($this->matches);
	}
}

class QueryCounter {
	public $queryCount = 0;
	public $queryDuration = 0;
	public $identifiedQueryCount = 0;
	public $identifiedQueryDuration = 0;
	public $selectCount = 0;
	public $selectDuration = 0;
	public $updateCount = 0;
	public $updateDuration = 0;
	public $insertCount = 0;
	public $insertDuration = 0;
	public $deleteCount = 0;
	public $deleteDuration = 0;
	
	function incrementQuery($duration) {
		$this->queryCount ++;
		$this->queryDuration += $duration;
	}
	
	function incrementIdentifiedQuery($duration) {
		$this->identifiedQueryCount ++;
		$this->identifiedQueryDuration += $duration;
	}
	
	function incrementSelect($duration) {
		$this->selectCount ++;
		$this->selectDuration += $duration;
	}
	
	function incrementUpdate($duration) {
		$this->updateCount ++;
		$this->updateDuration += $duration;
	}
	
	function incrementInsert($duration) {
		$this->insertCount ++;
		$this->insertDuration += $duration;
	}
	
	function incrementDelete($duration) {
		$this->deleteCount ++;
		$this->deleteDuration += $duration;
	}
	
	function getQueryCount() {
		return $this->queryCount;
	}
	
	function getQueryDuration() {
		return $this->queryDuration;
	}
	
	function getIdentifiedQueryCount() {
		return $this->identifiedQueryCount;
	}
	
	function getIdentifiedQueryDuration() {
		return $this->identifiedQueryDuration;
	}
	
	function getSelectCount() {
		return $this->selectCount;
	}
	
	function getSelectDuration() {
		return $this->selectDuration;
	}
	
	function getUpdateCount() {
		return $this->updateCount;
	}
	
	function getUpdateDuration() {
		return $this->updateDuration;
	}
	
	function getInsertCount() {
		return $this->insertCount;
	}
	
	function getInsertDuration() {
		return $this->insertDuration;
	}
	
	function getDeleteCount() {
		return $this->deleteCount;
	}
	
	function getDeleteDuration() {
		return $this->deleteDuration;
	}
}
