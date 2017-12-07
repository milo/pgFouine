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

class PostgreSQLParser {

	function & parse($text) {
		global $postgreSQLRegexps;
		
		$logLineMatch = $postgreSQLRegexps['LogLine']->match($text);

		if($logLineMatch) {
			$logLinePrefix = trim($logLineMatch->getMatch(1));
			$keyword = $logLineMatch->getMatch(2);
			$postMatch = $logLineMatch->getPostMatch();
			
			if($keyword == 'LOG' || $keyword == 'DEBUG') {
				$queryMatch = $postgreSQLRegexps['RegularQueryStartPart']->match($postMatch);
				if($queryMatch) {
					$line = new PostgreSQLQueryStartLine($queryMatch->getPostMatch());
				} elseif($durationMatch = $postgreSQLRegexps['DurationPart']->match($postMatch)) {
					$additionalInformation = trim($durationMatch->getPostMatch());
					if($additionalInformation == '') {
						$line = new PostgreSQLDurationLine(trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
					} else {
						$additionalInformation = $postgreSQLRegexps['QueryStartPart']->replace($additionalInformation, '');
						if($preparedStatementMatch = $postgreSQLRegexps['PreparedStatementPart']->match($additionalInformation)) {
							$action = strtolower($preparedStatementMatch->getMatch(1));
							$statementInformation = explode('/', $preparedStatementMatch->getMatch(2));
							if(count($statementInformation) > 1) {
								$preparedStatementName = $statementInformation[0];
								$portalName = $statementInformation[1];
							} else {
								$preparedStatementName = $statementInformation[0];
								$portalName = '';
							}
							$text = $preparedStatementMatch->getPostMatch();
							if($action == 'execute') {
								$line = new PostgreSQLPreparedStatementExecuteWithDurationLine($preparedStatementName, $portalName, $text, trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
							} else {
								$line = new PostgreSQLPreparedStatementUselessLine();
							}
						} else {
							$line = new PostgreSQLQueryStartWithDurationLine($additionalInformation, trim($durationMatch->getMatch(1)), $durationMatch->getMatch(2));
						}
					}
				} elseif($statusMatch = $postgreSQLRegexps['StatusPart']->match($postMatch)) {
					$line = new PostgreSQLStatusLine($postMatch);
				} elseif($preparedStatementMatch = $postgreSQLRegexps['PreparedStatementPart']->match($postgreSQLRegexps['QueryStartPart']->replace($postMatch, ''))) {
					$action = strtolower($preparedStatementMatch->getMatch(1));
					$statementInformation = explode('/', $preparedStatementMatch->getMatch(2));
					if(count($statementInformation) > 1) {
						$preparedStatementName = $statementInformation[0];
						$portalName = $statementInformation[1];
					} else {
						$preparedStatementName = $statementInformation[0];
						$portalName = '';
					}
					$text = $preparedStatementMatch->getPostMatch();
					if($action == 'execute' || $action == 'execute from fetch') {
						$line = new PostgreSQLPreparedStatementExecuteLine($preparedStatementName, $portalName, $text);
					} else {
						$line = new PostgreSQLPreparedStatementUselessLine();
					}
				} else {
					// we ignore a lot of common log lines as they are not interesting
					// but we still raise an error if we don't recognize a log line
					// as it may provide useful information about an unusual activity
					if(!CONFIG_QUIET && (
						strpos($postMatch, 'transaction ID wrap limit is') !== 0 &&
						strpos($postMatch, 'archived transaction log file') !== 0 &&
						strpos($postMatch, 'disconnection: session time: ') !== 0 &&
						strpos($postMatch, 'autovacuum: processing database') !== 0 &&
						strpos($postMatch, 'recycled transaction log file') !== 0 &&
						strpos($postMatch, 'removing transaction log file "') !== 0 &&
						strpos($postMatch, 'removing file "') !== 0 &&
						strpos($postMatch, 'could not receive data from client') !== 0 &&
						strpos($postMatch, 'checkpoints are occurring too frequently (') !== 0 &&
						strpos($postMatch, 'invalid length of startup packet') !== 0 &&
						strpos($postMatch, 'incomplete startup packet') !== 0
						)) {
						stderr('Unrecognized LOG or DEBUG line: '.$text, true);
					}
					$line = false;
				}
			} elseif($keyword == 'WARNING' || $keyword == 'ERROR' || $keyword == 'FATAL' || $keyword == 'PANIC') {
				$line = new PostgreSQLErrorLine($postMatch);
			} elseif($keyword == 'CONTEXT') {
				$line = new PostgreSQLContextLine($postMatch);
			} elseif($keyword == 'STATEMENT') {
				$line = new PostgreSQLStatementLine($postMatch);
			} elseif($keyword == 'HINT') {
				$line = new PostgreSQLHintLine($postMatch);
			} elseif($keyword == 'DETAIL') {
				$line = new PostgreSQLDetailLine($postMatch);
			} elseif($keyword == 'NOTICE') {
				$line = new PostgreSQLNoticeLine($postMatch);
			} elseif($keyword == 'LOCATION') {
				$line = new PostgreSQLLocationLine($postMatch);
			} else {
				$line = false;
			}
			if($line) {
				$line->setLogLinePrefix($logLinePrefix);
			}
		} else {
			// probably a continuation line. We let the PostgreSQLContinuationLine decide if it is one or not
			$line = new PostgreSQLContinuationLine($text);
		}
		return $line;
	}
}
