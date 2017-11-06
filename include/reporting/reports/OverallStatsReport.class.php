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

class OverallStatsReport extends Report {
	function OverallStatsReport(& $reportAggregator) {
		$this->Report($reportAggregator, 'Overall statistics', array('GlobalCountersListener'));
	}
	
	function getText() {
		$statsListener =& $this->reportAggregator->getListener('GlobalCountersListener');
		$normalizedListener =& $this->reportAggregator->getListener('NormalizedQueriesListener');
		$errorCountersListener =& $this->reportAggregator->getListener('GlobalErrorCountersListener');
		$normalizedErrorsListener =& $this->reportAggregator->getListener('NormalizedErrorsListener');
		
		$text = '';
		
		if($normalizedListener) {
			$text .= 'Number of unique normalized queries: '.$this->formatInteger($normalizedListener->getUniqueQueryCount())."\n";
		}
		$text .= 
			'Number of queries:     '.$this->formatInteger($statsListener->getQueryCount())."\n".
			'Total query duration:  '.$this->formatLongDuration($statsListener->getQueryDuration())."\n"
		;
		if($errorCountersListener) {
			$text .= 'Number of errors:     '.$this->formatInteger($errorCountersListener->getErrorCount())."\n";
			if($normalizedErrorsListener) {
				$text .= 'Number of unique normalized errors: '.$this->formatInteger($normalizedErrorsListener->getUniqueErrorCount())."\n";
			}
		}
		
		return $text;
	}
	
	function getHtml() {
		$statsListener =& $this->reportAggregator->getListener('GlobalCountersListener');
		$normalizedListener =& $this->reportAggregator->getListener('NormalizedQueriesListener');
		$errorCountersListener =& $this->reportAggregator->getListener('GlobalErrorCountersListener');
		$normalizedErrorsListener =& $this->reportAggregator->getListener('NormalizedErrorsListener');
		
		$html = '';
		
		$html .= '<ul>';
		if($normalizedListener) {
			$html .= '<li>Number of unique normalized queries: '.$this->formatInteger($normalizedListener->getUniqueQueryCount()).'</li>';
		}
		$html .= '<li>Number of queries: '.$this->formatInteger($statsListener->getQueryCount());
		if($statsListener->getQueryCount() != $statsListener->getIdentifiedQueryCount()) {
			$html .= ' (identified: '.$this->formatInteger($statsListener->getIdentifiedQueryCount()).')';
		}
		$html .= '</li>';
		$html .= '<li>Total query duration: '.$this->formatLongDuration($statsListener->getQueryDuration());
		if($statsListener->getQueryDuration() != $statsListener->getIdentifiedQueryDuration()) {
			$html .= ' (identified: '.$this->formatLongDuration($statsListener->getIdentifiedQueryDuration()).')';
		}
		$firstQueryTime = ($statsListener->getFirstQueryTimestamp() == MAX_TIMESTAMP) ? 'no query detected' : $this->formatTimestamp($statsListener->getFirstQueryTimestamp());
		$lastQueryTime = ($statsListener->getLastQueryTimestamp() == MIN_TIMESTAMP) ? 'no query detected' : $this->formatTimestamp($statsListener->getLastQueryTimestamp());
		$html .= '</li>';
		$html .= '<li>First query: '.$firstQueryTime.'</li>';
		$html .= '<li>Last query: '.$lastQueryTime.'</li>';
		$peakTimestamps = $statsListener->getQueryPeakTimestamps();
		if($peakTimestamps) {
			array_walk($peakTimestamps, array(&$this, 'walkFormatTimestamp'));
			$html .= '<li>Query peak: '.$this->formatInteger($statsListener->getQueryPeakQueryCount()).' queries/s at '.implode(', ', $peakTimestamps).'</li>';
		}
		if($errorCountersListener) {
			$html .= '<li>Number of errors: '.$this->formatInteger($errorCountersListener->getErrorCount()).'</li>';
			if($normalizedErrorsListener) {
				$html .= '<li>Number of unique normalized errors: '.$this->formatInteger($normalizedErrorsListener->getUniqueErrorCount()).'</li>';
			}
		}
		$html .= '</ul>';
		
		return $html;
	}
	
	function walkFormatTimestamp(& $value, $key) {
		$value = $this->formatTimestamp($value);
	}
}
