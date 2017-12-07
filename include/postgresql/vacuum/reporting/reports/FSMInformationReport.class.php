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

class FSMInformationReport extends Report {
	function __construct(& $reportAggregator) {
		parent::__construct($reportAggregator, 'FSM information', ['FSMInformationListener']);
	}
	
	function getText() {
		return 'This report doesn\'t support the text format at the moment.';
	}
	
	function getHtml() {
		$listener = $this->reportAggregator->getListener('FSMInformationListener');
		
		$fsmInformation = $listener->getFSMInformation();
		
		$html = '';
		
		if($fsmInformation) {
			$html .= '<ul>';
			$html .= '<li>FSM size: '.$this->formatInteger($fsmInformation->getSize()).' kB</li>';
			$html .= '</ul>';
			$html .= '<br />';
			
			$html .= '
<table class="queryList fsmInformation" style="width:40%">
	<tr>
		<th>&nbsp;</th>
		<th>Current value</th>
		<th>Limit</th>
		<th>Percentage</th>
	</tr>';
			
			$pageSlotsUsedPercentage = $this->getPercentage($fsmInformation->getPageSlotsRequired(), $fsmInformation->getMaxNumberOfPageSlots());
			$style = 'normal';
			if($pageSlotsUsedPercentage > 85) {
				$style = 'warning';
			}
			if($pageSlotsUsedPercentage > 99) {
				$style = 'fatal';
			}
			
			$html .= '<tr class="'.$this->getRowStyle(0).'">
				<th class="left">Page slots</th>
				<td class="right">'.$this->formatInteger($fsmInformation->getPageSlotsRequired()).'</td>
				<td class="right">'.$this->formatInteger($fsmInformation->getMaxNumberOfPageSlots()).'</td>
				<td class="right '.$style.'">'.$pageSlotsUsedPercentage.' %</td>
			</tr>';
			
			$relationSlotsUsedPercentage = $this->getPercentage($fsmInformation->getCurrentNumberOfRelations(), $fsmInformation->getMaxNumberOfRelations());
			$style = 'normal';
			if($relationSlotsUsedPercentage > 85) {
				$style = 'warning';
			}
			if($relationSlotsUsedPercentage > 99) {
				$style = 'error';
			}
			
			$html .= '<tr class="'.$this->getRowStyle(0).'">
				<th class="left">Relations</th>
				<td class="right">'.$this->formatInteger($fsmInformation->getCurrentNumberOfRelations()).'</td>
				<td class="right">'.$this->formatInteger($fsmInformation->getMaxNumberOfRelations()).'</td>
				<td class="right '.$style.'">'.$relationSlotsUsedPercentage.' %</td>
			</tr>';
			$html .= "\n";
			$html .= '</table>';
		} else {
			$html .= '<p>FSM information not available.</p>';
		}
		return $html;
	}
}
