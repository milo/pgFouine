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

class SlowestQueriesListener extends QueryListener {
	var $queryList;
	
	function SlowestQueriesListener() {
		$this->queryList = new SlowestQueryList(CONFIG_TOP_QUERIES_NUMBER);
	}
	
	function fireEvent(& $query) {
		$this->queryList->addQuery($query);
	}
	
	function & getSortedQueries() {
		$sortedQueries =& $this->queryList->getSortedQueries();
		return $sortedQueries;
	}
}

?>