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

class VacuumedTablesDetailsReport extends Report {
	function __construct(& $reportAggregator) {
		parent::__construct($reportAggregator, 'Vacuumed tables details', ['VacuumedTablesListener']);
	}
	
	function getText() {
		return 'This report doesn\'t support the text format at the moment.';
	}
	
	function getHtml() {
		$listener = $this->reportAggregator->getListener('VacuumedTablesListener');
		
		$vacuumedTables = $listener->getVacuumedTables();
		$vacuumedTablesCount = count($vacuumedTables);
		
		$html = '';
		
		for($i = 0; $i < $vacuumedTablesCount; $i++) {
			$vacuumedTable =& $vacuumedTables[$i];
			$html .= '<h3 id="vacuum-table-details-'.$vacuumedTable->getNumber().'">'.$vacuumedTable->getNumber().' - '.$vacuumedTable->getTablePath().'</h3>';
			
			$html .= '<div class="indexInformation">';
			$html .= '<ul>
				<li>Pages: '.$vacuumedTable->getNumberOfPages().'</li>';
			$html .= '<li>Pages truncated: '.$vacuumedTable->getNumberOfPagesRemoved().' ( '.$this->getPercentage($vacuumedTable->getNumberOfPagesRemoved(), $vacuumedTable->getNumberOfPages()).'% )</li>';
			$html .= '
				<li>Row versions: '.$vacuumedTable->getTotalNumberOfRows().'</li>
				<li>Removable row versions: '.$vacuumedTable->getNumberOfRemovableRows().' ( '.$this->getPercentage($vacuumedTable->getNumberOfRemovableRows(), $vacuumedTable->getTotalNumberOfRows()).'% )</li>
				<li>Non removable dead rows: '.$vacuumedTable->getNumberOfNonRemovableDeadRows().'</li>';
			if($vacuumedTable->getNonRemovableRowMinSize() != '-') {
				$html .= '<li>Non removable row size: from '.$vacuumedTable->getNonRemovableRowMinSize().' bytes to '.$vacuumedTable->getNonRemovableRowMaxSize().' bytes</li>';
			}
			$html .= '
				<li>Unused item pointers: '.$vacuumedTable->getNumberOfUnusedItemPointers().'</li>
				<li>CPU usage: sys: '.$this->formatLongDuration($vacuumedTable->getSystemCpuUsage()).' / user: '.$this->formatLongDuration($vacuumedTable->getUserCpuUsage()).'</li>
				<li>Duration: '.$this->formatLongDuration($vacuumedTable->getDuration()).'</li>				
			</ul>';
			$indexesInformation = $vacuumedTable->getIndexesInformation();
			$numberOfIndexes = count($indexesInformation);
			if($numberOfIndexes > 0) {
				$html .= '<table class="queryList">
					<tr>
						<th>Index</th>
						<th>Pages</th>
						<th>Deleted pages</th>
						<th>Reusable pages</th>
						<th>Row versions</th>
						<th>Removed row versions</th>
						<th>CPU usage</th>
						<th>Duration</th>
					</tr>';
				for($j = 0; $j < $numberOfIndexes; $j++) {
					$indexInformation = $indexesInformation[$j];
					
					$html .= '<tr class="'.$this->getRowStyle($j).'">
							<td>'.$indexInformation->getIndexName().'</td>
							<td class="right">'.$indexInformation->getNumberOfPages().'</td>
							<td class="right">'.$indexInformation->getNumberOfDeletedPages().'</td>
							<td class="right">'.$indexInformation->getNumberOfReusablePages().'</td>
							<td class="right">'.$indexInformation->getNumberOfRowVersions().'</td>
							<td class="right">'.$indexInformation->getNumberOfRemovedRowVersions().'</td>
							<td class="right">sys:&nbsp;'.$this->formatLongDuration($indexInformation->getSystemCpuUsage()).'&nbsp;/&nbsp;user:&nbsp;'.$this->formatLongDuration($indexInformation->getUserCpuUsage()).'</td>
							<td class="right">'.$this->formatLongDuration($indexInformation->getDuration()).'</td>
						</tr>';
					unset($indexInformation);
				}
				$html .= '</table>';
			}
			unset($indexesInformation);
			$html .= '</div>';
		}
		return $html;
	}
}
