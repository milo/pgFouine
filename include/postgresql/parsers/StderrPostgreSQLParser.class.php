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

class StderrPostgreSQLParser extends PostgreSQLParser {
	var $regexpStderrContext;
	
	var $timestamp = false;
	var $connectionId = false;
	var $commandNumber = false;
	var $lineNumber = false;
	var $tainted = false;
	
	function StderrPostgreSQLParser() {
		$this->regexpStderrContext = new RegExp('/^([0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2})(?: [A-Z]{2,4})? .*?\[(\d{1,7})\]: \[(\d{1,10})(?:\-(\d{1,5}))?\] /');
	}

	function & parse($data) {
		$contextMatch =& $this->regexpStderrContext->match($data);
		
		if($contextMatch === false) {
			if($this->lineNumber) {
				$text = $data;
				$timestamp = $this->timestamp;
				$connectionId = $this->connectionId;
				$commandNumber = $this->commandNumber;
				$lineNumber = ++$this->lineNumber;
			} else {
				$line = false;
				return $line;
			}
		} else {
			$matches = $contextMatch->getMatches();
			$text = $contextMatch->getPostMatch();
			
			if(count($matches) < 4 || !$text) {
				$line = false;
				return $line;
			}
			
			$formattedDate = $matches[1][0];
			$timestamp = strtotime($formattedDate);
			if($timestamp < 0) {
				$timestamp = 0;
			}
			
			$connectionId = $matches[2][0];
			$commandNumber = $matches[3][0];
			
			if(isset($matches[4][0])) {
				$lineNumber = $matches[4][0];
			} else {
				$lineNumber = 1;
			}
			
			$this->timestamp = $timestamp;
			$this->connectionId = $connectionId;
			$this->commandNumber = $commandNumber;
			$this->lineNumber = $lineNumber;
		}
		
		$line =& parent::parse($text);
		
		if($line) {
			$line->setContextInformation($timestamp, $connectionId, $commandNumber, $lineNumber);
		}
		return $line;
	}
}
