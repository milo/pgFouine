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

class SlowestQueriesReport extends Report {
	function __construct(& $reportAggregator) {
		parent::__construct($reportAggregator, 'Slowest queries', ['SlowestQueriesListener']);
	}
	
	function getText() {
		$listener =& $this->reportAggregator->getListener('SlowestQueriesListener');
		$text = '';
		
		$queries =& $listener->getSortedQueries();
		$count = count($queries);
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$text .= ($i+1).') '.$this->formatDuration($query->getDuration()).' '.CONFIG_DURATION_UNIT.' - '.$this->formatRealQuery($query)."\n";
			$text .= "--\n";
			
			unset($query);
		}
		return $text;
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('SlowestQueriesListener');
		$html = '
<table class="queryList">
	<tr>
		<th>Rank</th>
		<th>Duration&nbsp;('.CONFIG_DURATION_UNIT.')</th>
		<th>Query</th>
	</tr>';
		$queries =& $listener->getSortedQueries();
		$count = count($queries);
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$title = $query->getDetailedInformation();
			
			$html .= '<tr class="'.$this->getRowStyle($i).'">
				<td class="center top">'.($i+1).'</td>
				<td class="relevantInformation top center">'.$this->formatDuration($query->getDuration()).'</td>
				<td title="'.$query->getDetailedInformation().'">'.$this->formatRealQuery($query).'</td>
			</tr>';
			$html .= "\n";
			
			unset($query);
		}
		$html .= '</table>';
		return $html;
	}
}
