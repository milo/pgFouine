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

class PostgreSQLFSMInformationDetailLine extends PostgreSQLVacuumLogLine {

	function PostgreSQLFSMInformationDetailLine($text) {
		$this->PostgreSQLVacuumLogLine($text);
	}
	
	function appendTo(& $logObject) {
		global $postgreSQLVacuumRegexps;
		
		$detailMatch =& $postgreSQLVacuumRegexps['FSMDetailLine']->match($this->text);
		
		if($detailMatch) {
			$pageSlotsInUse = $detailMatch->getMatch(1);
			$pageSlotsRequired = $detailMatch->getMatch(2);
			$maxNumberOfPageSlots = $detailMatch->getMatch(3);
			$maxNumberOfRelations = $detailMatch->getMatch(4);
			$size = $detailMatch->getMatch(5);
		
			$logObject->setDetailedInformation($pageSlotsInUse,
				$pageSlotsRequired,
				$maxNumberOfPageSlots, $maxNumberOfRelations, $size);
		}
	}
}

?>