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

class QueriesHistoryPerPidReport extends Report {
	function __construct(& $reportAggregator) {
		$this->Report($reportAggregator, 'Queries history per PID', ['QueriesHistoryListener']);
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('QueriesHistoryListener');
		
		$queries =& $listener->getQueriesHistoryPerConnection();
		$count = count($queries);
		$currentConnectionId = 0;
		
		$html = '';
		
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			
			if($currentConnectionId != $query->getConnectionId()) {
				if($currentConnectionId != 0) {
					$html .= '</table>';
				}
				$html .= '<h3>Pid: '.$query->getConnectionId().'</h3>';
				$html .= '
					<table class="queryList">
						<tr>
							<th>Rank</th>
							<th>Time</th>
							<th>Query</th>
							<th>Duration&nbsp;('.CONFIG_DURATION_UNIT.')</th>
						</tr>';
				$currentConnectionId = $query->getConnectionId();
			}

			$title = $query->getDetailedInformation();
			
			$html .= '<tr class="'.$this->getRowStyle($i).'">
				<td class="center top">'.($i+1).'</td>
				<td class="top center">'.$this->formatTimestamp($query->getTimestamp()).'</td>
				<td title="'.$query->getDetailedInformation().'">'.$this->formatRealQuery($query).'</td>
				<td class="top center">'.$this->formatDuration($query->getDuration()).'</td>
			</tr>';
			$html .= "\n";
			
			unset($query);
		}
		if($currentConnectionId != 0) {
			$html .= '</table>';
		}
		
		return $html;
	}
}
