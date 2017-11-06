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

class PostgreSQLStatusLine extends PostgreSQLLogLine {
	function getLogObject(& $logStream) {
		global $postgreSQLRegexps;
		
		$connectionReceived =& $postgreSQLRegexps['ConnectionReceived']->match($this->text);
		if($connectionReceived) {
			$logStream->setHostConnection($connectionReceived->getMatch(1), $connectionReceived->getMatch(2));
			return false;
		}
		
		$connectionAuthorized =& $postgreSQLRegexps['ConnectionAuthorized']->match($this->text);
		if($connectionAuthorized) {
			$logStream->setUserDatabase($connectionAuthorized->getMatch(1), $connectionAuthorized->getMatch(2));
		}
		return false;
	}
	
	function complete() {
		return true;
	}
}

?>