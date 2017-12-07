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

class PostgreSQLPreparedStatementExecuteWithDurationLine extends PostgreSQLPreparedStatementExecuteLine {
	public $statementName;
	public $portalName;
	
	function __construct($statementName, $portalName, $text, $timeString, $unit) {
		parent::__construct($statementName, $portalName, $text, $this->parseDuration($timeString, $unit));
	}
	
	function & getLogObject(& $logStream) {
		$preparedStatement = parent::getLogObject($logStream);
		$preparedStatement->setDuration($this->duration);
		
		return $preparedStatement;
	}
	
	function complete() {
		return true;
	}
}
