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

class PostgreSQLVacuumDetailLine extends PostgreSQLVacuumLogLine {

	function PostgreSQLVacuumDetailLine($text) {
		$this->PostgreSQLVacuumLogLine($text);
	}
	
	function appendTo(& $logObject) {
		global $postgreSQLVacuumRegexps;
		
		$detailVacuumFullMatch =& $postgreSQLVacuumRegexps['VacuumFullDetailLine']->match($this->text);
		$detailVacuumMatch =& $postgreSQLVacuumRegexps['VacuumDetailLine']->match($this->text);
		
		if($detailVacuumFullMatch) {
			$nonRemovableDeadRows = $detailVacuumFullMatch->getMatch(1);
			$nonRemovableRowMinSize = $detailVacuumFullMatch->getMatch(2);
			$nonRemovableRowMaxSize = $detailVacuumFullMatch->getMatch(3);
			$unusedItemPointers = $detailVacuumFullMatch->getMatch(4);
			$totalFreeSpace = $detailVacuumFullMatch->getMatch(5);
			$numberOfPagesToEmpty = $detailVacuumFullMatch->getMatch(6);
			$numberOfPagesToEmptyAtTheEndOfTheTable = $detailVacuumFullMatch->getMatch(7);
			$numberOfPagesWithFreeSpace = $detailVacuumFullMatch->getMatch(8);
			$freeSpace = $detailVacuumFullMatch->getMatch(9);
			$systemCpuUsage = (float) $detailVacuumFullMatch->getMatch(10);
			$userCpuUsage = (float) $detailVacuumFullMatch->getMatch(11);
			$duration = (float) $detailVacuumFullMatch->getMatch(12);
			
			$logObject->setDetailedInformation($nonRemovableDeadRows,
				$nonRemovableRowMinSize, $nonRemovableRowMaxSize,
				$unusedItemPointers,
				$totalFreeSpace,
				$numberOfPagesToEmpty, $numberOfPagesToEmptyAtTheEndOfTheTable,
				$numberOfPagesWithFreeSpace, $freeSpace,
				$systemCpuUsage, $userCpuUsage, $duration);
		} elseif($detailVacuumMatch) {
			$nonRemovableDeadRows = $detailVacuumMatch->getMatch(1);
			$nonRemovableRowMinSize = '-';
			$nonRemovableRowMaxSize = '-';
			$unusedItemPointers = $detailVacuumMatch->getMatch(2);
			$totalFreeSpace = '-';
			$numberOfPagesToEmpty = $detailVacuumMatch->getMatch(3);
			$numberOfPagesToEmptyAtTheEndOfTheTable = '-';
			$numberOfPagesWithFreeSpace = '-';
			$freeSpace = '-';
			$systemCpuUsage = (float) $detailVacuumMatch->getMatch(4);
			$userCpuUsage = (float) $detailVacuumMatch->getMatch(5);
			$duration = (float) $detailVacuumMatch->getMatch(6);
			
			$logObject->setDetailedInformation($nonRemovableDeadRows,
				$nonRemovableRowMinSize, $nonRemovableRowMaxSize,
				$unusedItemPointers,
				$totalFreeSpace,
				$numberOfPagesToEmpty, $numberOfPagesToEmptyAtTheEndOfTheTable,
				$numberOfPagesWithFreeSpace, $freeSpace,
				$systemCpuUsage, $userCpuUsage, $duration);		
		}
	}
}
