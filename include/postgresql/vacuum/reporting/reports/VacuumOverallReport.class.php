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

class VacuumOverallReport extends Report {
	function __construct(& $reportAggregator) {
		$this->Report($reportAggregator, 'Vacuum overall statistics', ['VacuumOverallListener']);
	}
	
	function getText() {
		return 'This report doesn\'t support the text format at the moment.';
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('VacuumOverallListener');
		
		$statisticsPerDatabase = $listener->getStatisticsPerDatabase();
		$statistics = $listener->getStatistics();
		
		$html = '';
		
		$html .= '
<table class="queryList">
	<tr>
		<th>&nbsp;</th>
		<th style="padding-left:15px; padding-right:15px;">Tables</th>
		<th style="padding-left:15px; padding-right:15px;">Pages</th>
		<th style="padding-left:15px; padding-right:15px;">Pages truncated</th>
		<th style="width:50px;">%</th>
		<th style="padding-left:15px; padding-right:15px;">Row versions</th>
		<th style="padding-left:15px; padding-right:15px;">Removable row versions</th>
		<th style="width:50px;">%</th>
		<th>CPU usage</th>
		<th>Duration</th>
	</tr>';

		foreach($statisticsPerDatabase AS $database => $databaseStatistics) {		
			$html .= '<tr class="'.$this->getRowStyle(0).'">
				<th class="left" style="padding-right:15px;">'.$database.'</th>
				<td class="right">'.$this->formatInteger($databaseStatistics['numberOfTables']).'</td>
				<td class="right">'.$this->formatInteger($databaseStatistics['numberOfPages']).'</td>
				<td class="right">'.$this->formatInteger($databaseStatistics['numberOfPagesRemoved']).'</td>
				<td class="right">'.$this->getPercentage($databaseStatistics['numberOfPagesRemoved'], $databaseStatistics['numberOfPages']).'</td>
				<td class="right">'.$this->formatInteger($databaseStatistics['numberOfRowVersions']).'</td>
				<td class="right">'.$this->formatInteger($databaseStatistics['numberOfRemovableRowVersions']).'</td>
				<td class="right">'.$this->getPercentage($databaseStatistics['numberOfRemovableRowVersions'], $databaseStatistics['numberOfRowVersions']).'</td>
				<td class="right">'.$this->formatLongDuration($databaseStatistics['cpuUsage']).'</td>
				<td class="right">'.$this->formatLongDuration($databaseStatistics['duration']).'</td>
			</tr>';
		}
		if(count($statisticsPerDatabase) > 1) {
			$html .= '<tr class="'.$this->getRowStyle(1).'">
					<th class="left" style="padding-right:15px;">Overall</th>
					<td class="right">'.$this->formatInteger($statistics['numberOfTables']).'</td>
					<td class="right">'.$this->formatInteger($statistics['numberOfPages']).'</td>
					<td class="right">'.$this->formatInteger($statistics['numberOfPagesRemoved']).'</td>
					<td class="right">'.$this->getPercentage($statistics['numberOfPagesRemoved'], $statistics['numberOfPages']).'</td>
					<td class="right">'.$this->formatInteger($statistics['numberOfRowVersions']).'</td>
					<td class="right">'.$this->formatInteger($statistics['numberOfRemovableRowVersions']).'</td>
					<td class="right">'.$this->getPercentage($statistics['numberOfRemovableRowVersions'], $statistics['numberOfRowVersions']).'</td>
					<td class="right">'.$this->formatLongDuration($statistics['cpuUsage']).'</td>
					<td class="right">'.$this->formatLongDuration($statistics['duration']).'</td>
				</tr>';
		}
		$html .= '</table>';
		return $html;
	}
}
