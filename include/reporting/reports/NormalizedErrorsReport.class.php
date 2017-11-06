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

class NormalizedErrorsReport extends Report {
	function __construct(& $reportAggregator, $title, $needs = []) {
		$needs = array_merge(['NormalizedErrorsListener', 'GlobalErrorCountersListener'], $needs);
		parent::__construct($reportAggregator, $title.' (N)', $needs);
	}
	
	function getNormalizedErrorWithExamplesHtml($counter, & $normalizedError) {
		$html = '';
		
		$examples =& $normalizedError->getFilteredExamplesArray();
		$exampleCount = count($examples);
		
		if($exampleCount) {		
			$buttonId = 'button_'.$this->getReportClass().'_'.$counter;
			$divId = 'examples_'.$this->getReportClass().'_'.$counter;
			
			$html .= '<input type="button" class="examplesButton" id="'.$buttonId.'" name="'.$buttonId.'" value="Show examples" onclick="javascript:toggle(\''.$buttonId.'\', \''.$divId.'\', \'examples\');" />';
			$html .= '<div id="'.$divId.'" class="examples" style="display:none;">';
			
			
			for($i = 0; $i < $exampleCount; $i++) {
				$example =& $examples[$i];
				$title = $example->getDetailedInformation();
				
				$text = $example->getText();
				if($example->isTextAStatement()) {
					$text = $this->highlightSql($text);
				}
				$html .= '<div class="example'.($i%2).'" title="'.$title.'">';
				$html .= $text;
				$html .= '</div>';
				unset($example);
			}
			$html .= '</div>';
		}
		
		return $html;
	}
	
	function getHourlyStatisticsTooltip(& $normalizedError) {
		$html = '';
		$html .= '<div class="tooltip">';
		$hourlyStatistics = $normalizedError->getHourlyStatistics();
		$html .= '<table>
	<tr>
		<th>Day</th>
		<th>Time</th>
		<th>Count</th>
	</tr>';
		$i = 0;
		foreach($hourlyStatistics AS $hour => $hourlyCounter) {
			$hourTimestamp = strtotime($hour);
			if(date('H', $hourTimestamp) == 0 || $i == 0) {
				$day = date('M j', $hourTimestamp);
			} else {
				$day = '&nbsp;';
			}
			$html .= '<tr class="row'.($i % 2).'">
		<td>'.$day.'</td>
		<td>'.date('ga', $hourTimestamp).'</td>
		<td>'.$this->formatInteger($hourlyCounter['count']).'</td>
	</tr>';
			$i++;
		}
		$html .= '</table>';
		$html .= '</div>';
		return $html;
	}
}
