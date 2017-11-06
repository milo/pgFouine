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

class NormalizedQueriesMostTimeReport extends NormalizedReport {
	function NormalizedQueriesMostTimeReport(& $reportAggregator) {
		$this->NormalizedReport($reportAggregator, 'Queries that took up the most time');
	}
	
	function getText() {
		$listener =& $this->reportAggregator->getListener('NormalizedQueriesListener');
		$text = '';
		
		$queries =& $listener->getQueriesMostTime();
		
		$count = count($queries);
		
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$text .= ($i+1).') '.$this->formatLongDuration($query->getTotalDuration()).' - '.$this->formatInteger($query->getTimesExecuted()).' - '.$this->shortenQueryText($query->getNormalizedText())."\n";
			$text .= "--\n";
		}
		return $text;
	}
	
	function getHtml() {
		$listener =& $this->reportAggregator->getListener('NormalizedQueriesListener');
		$html = '
<table class="queryList">
	<tr>
		<th>Rank</th>
		<th>Total duration</th>
		<th>Times executed</th>
		<th>Av.&nbsp;duration&nbsp;('.CONFIG_DURATION_UNIT.')</th>
		<th>Query</th>
	</tr>';
		$queries =& $listener->getQueriesMostTime();
		$count = count($queries);
		
		for($i = 0; $i < $count; $i++) {
			$query =& $queries[$i];
			$html .= '<tr class="'.$this->getRowStyle($i).'">
				<td class="center top">'.($i+1).'</td>
				<td class="relevantInformation top center">'.$this->formatLongDuration($query->getTotalDuration()).'</td>
				<td class="top center"><div class="tooltipLink"><span class="information">'.$this->formatInteger($query->getTimesExecuted()).'</span>'.$this->getHourlyStatisticsTooltip($query).'</div></td>
				<td class="top center">'.$this->formatDuration($query->getAverageDuration()).'</td>
				<td>'.$this->getNormalizedQueryWithExamplesHtml($i, $query).'</td>
			</tr>';
			$html .= "\n";
		}
		$html .= '</table>';
		return $html;
	}
}

?>