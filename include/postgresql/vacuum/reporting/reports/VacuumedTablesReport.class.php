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

class VacuumedTablesReport extends Report {
	function VacuumedTablesReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Vacuumed tables', array('VacuumedTablesListener'));
		
		$reportAggregator->addScript('sorttable.js');
	}
	
	function getText() {
		return 'This report doesn\'t support the text format at the moment.';
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('VacuumedTablesListener');
		
		$vacuumedTables =& $listener->getVacuumedTablesSortedByPercentageOfRowVersionsRemoved();
		$vacuumedTablesCount = count($vacuumedTables);
		
		$html = '';
		
		$html .= '
<p class="tip">Click on a column header to sort the rows. Note that it can be quite long to sort all the rows if you vacuumed a lot of tables.</p>
<table class="queryList sortable" id="sortableVacuumResults">
	<tr>
		<th>#</th>
		<th>Table</th>
		<th>Pages</th>
		<th>Pages truncated</th>
		<th style="width:50px;">%</th>
		<th>Row versions</th>
		<th>Removable row versions</th>
		<th style="width:50px;">%</th>
		<th>CPU usage</th>
		<th>Duration</th>';
		if($this->reportAggregator->containsReportBlock('VacuumedTablesDetailsReport')) {
			$html .= '<th>Details</th>';
		}
		$html .= '
	</tr>';

		for($i = 0; $i < $vacuumedTablesCount; $i++) {
			$vacuumedTable =& $vacuumedTables[$i];
			$html .= '<tr class="'.$this->getRowStyle($i).'">
				<td>'.$vacuumedTable->getNumber().'</td>
				<td>'.$vacuumedTable->getTablePath().'</td>
				<td class="right">'.$vacuumedTable->getNumberOfPages().'</td>
				<td class="right">'.$vacuumedTable->getNumberOfPagesRemoved().'</td>
				<td class="right">'.$this->getPercentage($vacuumedTable->getNumberOfPagesRemoved(), $vacuumedTable->getNumberOfPages()).'</td>
				<td class="right">'.$vacuumedTable->getTotalNumberOfRows().'</td>
				<td class="right">'.$vacuumedTable->getNumberOfRemovableRows().'</td>
				<td class="right">'.$this->getPercentage($vacuumedTable->getNumberOfRemovableRows(), $vacuumedTable->getTotalNumberOfRows()).'</td>
				<td class="right" title="sys: '.$this->formatLongDuration($vacuumedTable->getSystemCpuUsage()).' / user: '.$this->formatLongDuration($vacuumedTable->getUserCpuUsage()).'">'.$this->formatDuration($vacuumedTable->getCpuUsage(), 2, '.', '').'</td>				
				<td class="right" title="'.$this->formatLongDuration($vacuumedTable->getDuration()).'">'.$this->formatDuration($vacuumedTable->getDuration(), 2, '.', '').'</td>';
				
			if($this->reportAggregator->containsReportBlock('VacuumedTablesDetailsReport')) {
				$html .= '<td class="center"><a href="#vacuum-table-details-'.$vacuumedTable->getNumber().'">Details</a></td>';
			}
			$html .= '</tr>';
		}
		$html .= '</table>';
		return $html;
	}
}

?>