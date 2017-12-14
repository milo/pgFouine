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

class Report {
	public $reportAggregator;
	public $title = '';
	public $needs = array();
	public $displayTitle = true;
	
	function __construct(& $reportAggregator, $title, $needs, $displayTitle = true) {
		$this->reportAggregator =& $reportAggregator;
		$this->title = $title;
		$this->needs = $needs;
		$this->displayTitle = $displayTitle;
	}
	
	function getTitle() {
		return $this->title;
	}
	
	function getNeeds() {
		return $this->needs;
	}
	
	function getTextTitle() {
		if($this->displayTitle) {
			$title = "\n#####  ".$this->title."  #####\n\n";
		} else {
			$title = '';
		}
		return $title;
	}
	
	function getHtmlTitle() {
		if($this->displayTitle) {
			$title = '<h2 id="'.$this->getReportClass().'">'.$this->title.' <a href="#top" title="Back to top">^</a></h2>';
		} else {
			$title = '';
		}
		return $title;
	}
	
	function pad($string, $length) {
		return $this->reportAggregator->pad($string, $length);
	}
	
	function getPercentage($number, $total) {
		return $this->reportAggregator->getPercentage($number, $total);
	}
	
	function formatInteger($integer) {
		return $this->reportAggregator->formatInteger($integer);
	}
	
	function formatTimestamp($timestamp) {
		return $this->reportAggregator->formatTimestamp($timestamp);
	}
	
	function getDurationForUnit($duration) {
		return $this->reportAggregator->getDurationForUnit($duration);
	}
	
	function formatDuration($duration, $decimals = 2, $decimalPoint = '.', $thousandSeparator = ',') {
		return $this->reportAggregator->formatDuration($duration, $decimals, $decimalPoint, $thousandSeparator);
	}
	
	function formatLongDuration($duration) {
		return $this->reportAggregator->formatLongDuration($duration);
	}
	
	function getReportClass() {
		return get_class($this);
	}
	
	function getRowStyle($i) {
		return 'row'.($i%2);
	}
	
	function highlightSql($sql, $prepend = '', $append = '') {
		return $this->reportAggregator->highlightSql($sql, $prepend, $append);
	}
	
	function formatRealQuery(& $query, $prepend = '', $append = '') {
		return $this->reportAggregator->formatRealQuery($query, $prepend, $append);
	}
	
	function shortenQueryText($queryText) {
		return $this->reportAggregator->shortenQueryText($queryText);
	}
}
