<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2006 Open Wide
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

class PostgreSQLVacuumParser extends PostgreSQLParser {
	var $regexpSyslogContext;
	
	function SyslogPostgreSQLParser($syslogString = CONFIG_SYSLOG_IDENTITY) {
		$this->regexpSyslogContext = new RegExp('/^([A-Z][a-z]{2} [ 0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}) .*? '.$syslogString.'\[(\d{1,5})\]: \[(\d{1,20})(?:\-(\d{1,5}))?\] /');
	}
	
	function & parse($text) {
		global $postgreSQLRegexps, $postgreSQLVacuumRegexps;

		$line = false;
		
		$logLineMatch =& $postgreSQLRegexps['LogLine']->match($text);

		if($logLineMatch) {
			$logLinePrefix = trim($logLineMatch->getMatch(1));
			$keyword = $logLineMatch->getMatch(2);
			$postMatch = $logLineMatch->getPostMatch();
			
			if($keyword == 'INFO') {
				$actionOnTableMatch =& $postgreSQLVacuumRegexps['VacuumingOrAnalyzingTable']->match($postMatch);
				$removableInformationMatch =& $postgreSQLVacuumRegexps['RemovableInformation']->match($postMatch);
				$operationInformationMatch =& $postgreSQLVacuumRegexps['OperationInformation']->match($postMatch);
				$fsmInformationMatch =& $postgreSQLVacuumRegexps['FSMInformation']->match($postMatch);
				$indexInformationMatch =& $postgreSQLVacuumRegexps['IndexCleanupInformation']->match($postMatch);
				
				if($actionOnTableMatch) {
					$matchCount = $actionOnTableMatch->getMatchCount();
					
					if($actionOnTableMatch->getMatch(1) == 'vacuuming') {
						$lineType = 'PostgreSQLVacuumingTableLine';
					} else {
						$lineType = 'PostgreSQLAnalyzingTableLine';
					}
					
					if($matchCount == 3) {
						$schema = 'public';
						$table = $actionOnTableMatch->getMatch(2);
					} else {
						$schema = $actionOnTableMatch->getMatch(2);
						$table = $actionOnTableMatch->getMatch(3);
					}
					
					$line = new $lineType($schema, $table);
				} elseif($removableInformationMatch) {
					$numberOfRemovableRows = $removableInformationMatch->getMatch(1);
					$numberOfNonRemovableRows = $removableInformationMatch->getMatch(2);
					$numberOfPages = $removableInformationMatch->getMatch(3);
					
					$line = new PostgreSQLVacuumRemovableInformationLine($numberOfRemovableRows, $numberOfNonRemovableRows, $numberOfPages);
				} elseif($operationInformationMatch) {
					$numberOfRowVersionsMoved = $operationInformationMatch->getMatch(1);
					$numberOfPagesRemoved = $operationInformationMatch->getMatch(2) - $operationInformationMatch->getMatch(3);
					
					$line = new PostgreSQLVacuumOperationInformationLine($numberOfRowVersionsMoved, $numberOfPagesRemoved);
				} elseif($fsmInformationMatch) {
					$currentNumberOfPages = $fsmInformationMatch->getMatch(1);
					$currentNumberOfRelations = $fsmInformationMatch->getMatch(2);
					
					$line = new PostgreSQLFSMInformationLine($currentNumberOfPages, $currentNumberOfRelations);
				} elseif($indexInformationMatch) {
					$indexName = $indexInformationMatch->getMatch(1);
					$numberOfRowVersions = $indexInformationMatch->getMatch(2);
					$numberOfPages = $indexInformationMatch->getMatch(3);
					
					$line = new PostgreSQLIndexCleanupInformationLine($indexName, $numberOfRowVersions, $numberOfPages);
				}
			} elseif($keyword == 'DETAIL') {
				$vacuumDetailMatch =& $postgreSQLVacuumRegexps['VacuumDetail']->match($postMatch);
				$cpuDetailMatch =& $postgreSQLVacuumRegexps['CpuDetailLine']->match($postMatch);
				$fsmInformationDetailMatch =& $postgreSQLVacuumRegexps['FSMInformationDetail']->match($postMatch);
				$indexDetail1Match =& $postgreSQLVacuumRegexps['IndexCleanupDetail1']->match($postMatch);
				$indexDetail2Match =& $postgreSQLVacuumRegexps['IndexCleanupDetail2']->match($postMatch);
				
				if($vacuumDetailMatch) {
					$line = new PostgreSQLVacuumDetailLine($postMatch);
				} elseif($cpuDetailMatch) {
					$systemCpuUsage = (float) $cpuDetailMatch->getMatch(1);
					$userCpuUsage = (float) $cpuDetailMatch->getMatch(2);
					$duration = (float) $cpuDetailMatch->getMatch(3);
					$line = new PostgreSQLVacuumCpuDetailLine($systemCpuUsage, $userCpuUsage, $duration);
				} elseif($fsmInformationDetailMatch) {
					$line = new PostgreSQLFSMInformationDetailLine($postMatch);
				} elseif($indexDetail1Match || $indexDetail2Match) {
					$line = new PostgreSQLIndexCleanupDetailLine($postMatch);
				}
			}
		} else {
			$vacuumingDatabaseMatch =& $postgreSQLVacuumRegexps['VacuumingDatabase']->match($text);
			$vacuumEndMatch =& $postgreSQLVacuumRegexps['VacuumEnd']->match($text);
			
			if($vacuumingDatabaseMatch) {
				$line = new PostgreSQLVacuumingDatabaseLine($vacuumingDatabaseMatch->getMatch(1));
			} elseif($vacuumEndMatch) {
				$line = new PostgreSQLVacuumEndLine();
			} else {
				// probably a continuation line
				$line = new PostgreSQLVacuumContinuationLine($text);
			}
		}
		return $line;
	}
}

?>