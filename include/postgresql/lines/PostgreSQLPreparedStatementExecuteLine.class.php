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

class PostgreSQLPreparedStatementExecuteLine extends PostgreSQLLogLine {
	public $statementName;
	public $portalName;
	public $parameters = [];
	
	function __construct($statementName, $portalName, $text, $duration = false) {
		parent::__construct($text, $duration);
		
		$this->statementName = $statementName;
		$this->portalName = $portalName;
		
		if(substr(trim($text), 0, 1) == '(') {
			$this->parameters = $this->parseParameters(trim($text, ' ();'));
		}
	}
	
	function & getLogObject(& $logStream) {
		$database = $this->database ? $this->database : $logStream->getDatabase();
		$user = $this->user ? $this->user : $logStream->getUser();
		
		$preparedStatement = new PreparedStatementLogObject($this->getConnectionId(), $user, $database, $this->statementName, $this->portalName, $this->text, $this->ignore);
		$preparedStatement->setContextInformation($this->timestamp, $this->commandNumber);
		$preparedStatement->setParameters($this->parameters);
		
		return $preparedStatement;
	}
	
	function parseParameters($parameters) {
		$parametersLength = strlen($parameters);
		$parametersArray = [];
		$currentParameter = '';
		$quote = false;
		$escape = false;
	
		for($i = 0; $i < $parametersLength; $i++) {
			$char = $parameters{$i};
			if($char == '\'') {
				if($escape) {
					$escape = false;
				} else {
					$quote = !$quote;
				}
			} elseif($char == '\\') {
				$escape = !$escape;
			} elseif($char == ',') {
				if(!$quote) {
					$parametersArray[] = trim($currentParameter);
					$currentParameter = '';
					continue;
				}
			} else {
				$escape = false;
			}
			$currentParameter .= $char;
		}
		if(strlen($currentParameter) > 0) {
			$parametersArray[] = trim($currentParameter);
		}
		return $parametersArray;
	}
}
