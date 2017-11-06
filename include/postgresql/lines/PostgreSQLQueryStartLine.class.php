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

class PostgreSQLQueryStartLine extends PostgreSQLLogLine {
	function __construct($text, $duration = false) {
		$this->PostgreSQLLogLine($this->filterQuery($text), $duration);
	}

	// TODO: remove this test from here and implement a -ignore option
	function filterQuery($text) {
		$loweredText = strtolower(trim($text));
		$this->ignore = (strpos($loweredText, 'begin') !== false) || (strpos($loweredText, 'vacuum') !== false) || ($loweredText == 'select 1');
		return $text;
	}
	
	function & getLogObject(& $logStream) {
		$database = $this->database ? $this->database : $logStream->getDatabase();
		$user = $this->user ? $this->user : $logStream->getUser();
		
		$query = new QueryLogObject($this->getConnectionId(), $user, $database, $this->text, $this->ignore);
		$query->setContextInformation($this->timestamp, $this->commandNumber);
		
		return $query;
	}
	
	function appendTo(& $logObject) {
		$query = new QueryLogObject($this->getConnectionId(), $logObject->getUser(), $logObject->getDatabase(), $this->text, $this->ignore);
		$query->setContextInformation($this->timestamp, $this->commandNumber);
		
		$logObject->addSubQuery($query);
	}
	
	function appendText($text) {
		if(CONFIG_KEEP_FORMATTING && (substr($text, 0, 1) == "\t" || substr($text, 0, 2) == '  ') && strlen($this->text) > 0) {
			$this->text .= "\n";
		}
		$this->text .= $text;
	}
}
