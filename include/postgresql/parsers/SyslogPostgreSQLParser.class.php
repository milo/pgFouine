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

class SyslogPostgreSQLParser extends PostgreSQLParser {
	var $regexpSyslogContext;
	
	function SyslogPostgreSQLParser($syslogString = CONFIG_SYSLOG_IDENTITY) {
		$this->regexpSyslogContext = new RegExp('/^((?:[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2})|(?:(?:[0-9]{4} )?[A-Z][a-z]{2} [ 0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}))..*? '.$syslogString.'\[(\d{1,7})\]: .*?\[(\d{1,20})(?:\-(\d{1,5}))?\] /');
	}

	function & parse($data) {
		$syslogContextMatch =& $this->regexpSyslogContext->match($data);
		if($syslogContextMatch === false) {
			$line = false;
			return $line;
		}
		
		$matches = $syslogContextMatch->getMatches();
		$text = $syslogContextMatch->getPostMatch();
		
		if(count($matches) < 4 || !$text) {
			$line = false;
			return $line;
		}
		
		$formattedDate = $matches[1][0];
		
		$timestamp = $this->getTimestampFromFormattedDate($formattedDate);
		
		$connectionId = $matches[2][0];
		$commandNumber = $matches[3][0];
		
		if(isset($matches[4][0])) {
			$lineNumber = $matches[4][0];
		} else {
			$lineNumber = 1;
		}
		
		$line =& parent::parse($text);
		
		if($line) {
			$line->setContextInformation($timestamp, $connectionId, $commandNumber, $lineNumber);
		}
		return $line;
	}
	
	function getTimestampFromFormattedDate($formattedDate) {
		$matches = array();
		if(preg_match('/^[0-9]{4}([- ])/', $formattedDate, $matches)) {
			if ($matches[1] == ' ') {
				$timestamp = strtotime(preg_replace('/^([0-9]{4} )([a-z]{3}[ ]+[0-9]{1,2})/i', '\2 \1', $formattedDate));
			} else {
				$timestamp = strtotime($formattedDate);
			}
		} else {
			$dateFormat = '/(^[a-z]{3}[ ]+[0-9]{1,2})/i';
			
			$timestamp = strtotime(preg_replace($dateFormat, '\1 '.date('Y'), $formattedDate));
	
			if($timestamp > time()) {
				$timestamp = strtotime(preg_replace($dateFormat, '\1 '.(date('Y')-1), $formattedDate));
			}
		}
		if($timestamp < 0) {
			$timestamp = 0;
		}
		return $timestamp;
	}
}

?>