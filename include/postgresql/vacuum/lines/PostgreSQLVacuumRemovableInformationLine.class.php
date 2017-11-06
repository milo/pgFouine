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

class PostgreSQLVacuumRemovableInformationLine extends PostgreSQLVacuumLogLine {
	public $numberOfRemovableRows;
	public $numberOfNonRemovableRows;
	public $numberOfPages;

	function __construct($numberOfRemovableRows, $numberOfNonRemovableRows, $numberOfPages) {
		parent::__construct();
		
		$this->numberOfRemovableRows = $numberOfRemovableRows;
		$this->numberOfNonRemovableRows = $numberOfNonRemovableRows;
		$this->numberOfPages = $numberOfPages;
	}
	
	function appendTo(& $logObject) {
		$logObject->setNumberOfRemovableRows($this->numberOfRemovableRows);
		$logObject->setNumberOfNonRemovableRows($this->numberOfNonRemovableRows);
		$logObject->setNumberOfPages($this->numberOfPages);
	}
}
