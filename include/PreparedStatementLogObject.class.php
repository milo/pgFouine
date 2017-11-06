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

class PreparedStatementLogObject extends QueryLogObject {
	public $name;
	public $portalName;
	public $parameters = [];
	
	function __construct($connectionId, $user, $db, $name, $portalName, $text = '', $ignored = false) {
		$this->QueryLogObject($connectionId, $user, $db, $text, $ignored);
	}
	
	function appendDetail($detail) {
		global $postgreSQLRegexps;
		
		// if we use queries, the text of the query is in the DETAIL line
		$prepareDetailMatch =& $postgreSQLRegexps['PrepareDetail']->match($detail);
		if($prepareDetailMatch) {
			$this->text = $prepareDetailMatch->getPostMatch();
		}
		
		// if we use the v3 protocol, bind information are in the DETAIL line below the execute line
		$bindDetailMatch =& $postgreSQLRegexps['BindDetail']->match($detail);
		if($bindDetailMatch) {
			$bindParametersMatch = $postgreSQLRegexps['BindParameters']->matchAll($bindDetailMatch->getPostMatch());

			$replace = [];
			
			for($i = 0; $i < count($bindParametersMatch); $i++) {
				$key = $bindParametersMatch[$i][1];
				$value = $bindParametersMatch[$i][2];
				if(substr($value, 0, 1) == "'") {
					$trimmedValue = substr($bindParametersMatch[$i][2], 1, -1);
					if(is_numeric($trimmedValue)) {
						$value = $trimmedValue;
					}
				}
				$this->parameters[$key] = $value;
			}
		}
		
		$this->buildQueryText();
	}
	
	function setParameters($parameters) {
		$this->parameters = [];
		for($i = 0; $i < count($parameters); $i++) {
			$this->parameters['$'.($i+1)] = $parameters[$i];
		}
		
		$this->buildQueryText();
	}
	
	function buildQueryText() {
		if(count($this->parameters) > 0) {
			foreach($this->parameters as $key => $value) {
				$trimmedValue = trim($value, "'");
				if(is_numeric($trimmedValue)) {
					$replace[$key] = $trimmedValue;
				} else {
					$replace[$key] = $value;
				}
			}
			$this->text = strtr($this->text, $replace);
		}
	}
}
