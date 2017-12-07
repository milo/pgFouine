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

class PostgreSQLLogLine {
	public $timestamp = false;
	public $connectionId = false;
	public $commandNumber = false;
	public $lineNumber = false;
	public $text;
	public $duration;
	public $ignore;
	public $database = false;
	public $user = false;
	
	function __construct($text = '', $duration = false) {
		$this->text = rtrim($text);
		$this->duration = $duration;
		
		if(DEBUG > 1 && !$text) stderr('Empty text for line', true);
	}
	
	function appendText($text) {
		$this->text .= $text;
	}

	function getText() {
		return $this->text;
	}

	function parseDuration($timeString, $unit) {
		if($unit == 'ms') {
			$duration = (floatval($timeString) / 1000);
		} elseif($unit == 'us') {
			$duration = (floatval($timeString) / 1000000);
		} else {
			$duration = floatval($timeString);
		}
		return $duration;
	}
	
	function getLogObject(& $logStream) {
		return false;
	}
	
	function appendTo(& $logObject) {
		return false;
	}
	
	function setContextInformation($timestamp, $connectionId, $commandNumber, $lineNumber) {
		$this->timestamp = $timestamp;
		$this->connectionId = $connectionId;
		$this->commandNumber = $commandNumber;
		$this->lineNumber = $lineNumber;
	}
	
	function setConnectionInformation($database, $user) {
		$this->database = $database;
		$this->user = $user;
	}
	
	function getTimestamp() {
		return $this->timestamp;
	}
	
	function getConnectionId() {
		return $this->connectionId;
	}
	
	function getCommandNumber() {
		return $this->commandNumber;
	}
	
	function getLineNumber() {
		return $this->lineNumber;
	}
	
	function getDatabase() {
		return $this->database;
	}
	
	function getUser() {
		return $this->user;
	}
	
	function complete() {
		return false;
	}
	
	function isIgnored() {
		return $this->ignore;
	}
	
	function getDuration() {
		return $this->duration;
	}
	
	function setLogLinePrefix($logLinePrefix) {
		global $postgreSQLRegexps;
		
		$logPrefixValues = $postgreSQLRegexps['LogLinePrefix']->matchAll($logLinePrefix);
		for($i = 0, $max = count($logPrefixValues); $i < $max; $i++) {
			if($logPrefixValues[$i][1] == 'db') {
				$this->database = $logPrefixValues[$i][2];
			} elseif($logPrefixValues[$i][1] == 'user') {
				$this->user = $logPrefixValues[$i][2];
			}
		}
	}
	
	function isContextual() {
		return false;
	}
}
