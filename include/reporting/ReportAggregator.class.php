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

class ReportAggregator {
	public $reportBlocks = [];
	public $logReader;
	public $outputFilePath;
	
	function ReportAggregator(& $logReader, $outputFilePath = false) {
		$this->logReader =& $logReader;
		$this->outputFilePath = $outputFilePath;
	}
	
	function addReportBlock($reportBlockName) {
		$reportBlock = new $reportBlockName($this);
		$this->reportBlocks[] =& $reportBlock;
	}
	
	function & getListener($listenerName) {
		return $this->logReader->getListener($listenerName);
	}
	
	function getOutput() {
		$output = '';
		$output .= $this->getHeader();
		$output .= $this->getBody();
		$output .= $this->getFooter();
		
		return $output;
	}
	
	function output() {
		if($this->outputFilePath) {
			$outputFilePointer = @fopen($this->outputFilePath, 'w');
			if($outputFilePointer) {
				fwrite($outputFilePointer, $this->getOutput());
				fclose($outputFilePointer);
			} else {
				stderr('cannot open file '.$this->outputFilePath.' for writing');
			}
		} else {
			echo $this->getOutput();
		}
	}
	
	function getNeeds() {
		$needs = [];
		$count = count($this->reportBlocks);
		for($i = 0; $i < $count; $i++) {
			$needs = array_merge($needs, $this->reportBlocks[$i]->getNeeds());
		}
		$needs = array_unique($needs);
		return $needs;
	}
	
	function getFileName() {
		return $this->logReader->getFileName();
	}
	
	function getTimeToParse() {
		return $this->logReader->getTimeToParse();
	}
	
	function getLineParsedCount() {
		return $this->logReader->getLineParsedCount();
	}
	
	function getFirstLineTimestamp() {
		return $this->logReader->getFirstLineTimestamp();
	}
	
	function getLastLineTimestamp() {
		return $this->logReader->getLastLineTimestamp();
	}
	
	function pad($string, $length) {
		return str_pad($string, $length, ' ', STR_PAD_LEFT);
	}
	
	function getPercentage($number, $total) {
		if($total > 0) {
			$percentage = $number*100/$total;
		} else {
			$percentage = 0;
		}
		return $this->pad(number_format($percentage, 1), 5);
	}
	
	function formatInteger($integer) {
		return number_format($integer);
	}
	
	function formatTimestamp($timestamp) {
		return formatTimestamp($timestamp);
	}
	
	function getDurationForUnit($duration) {
		if(CONFIG_DURATION_UNIT == 'ms') {
			$duration = $duration * 1000;
		}
		return $duration;
	}
	
	function formatDuration($duration, $decimals = 2, $decimalPoint = '.', $thousandSeparator = ',') {
		if(CONFIG_DURATION_UNIT == 'ms') {
			$duration = $duration * 1000;
		}
		return number_format($duration, $decimals, $decimalPoint, $thousandSeparator);
	}
	
	function formatLongDuration($duration, $decimals = 1) {
		$formattedDuration = '';
		
		if($duration > 60) {
			$duration = intval($duration);
			if($duration > 3600) {
				$formattedDuration .= intval($duration/3600).'h';
				$duration = $duration % 3600;
			}
			if($duration > 59) {
				$minutes = intval($duration/60);
				if(!empty($formattedDuration)) {
					$minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
				}
				$formattedDuration .= $minutes.'m';
				$duration = $duration % 60;
			}
			if($duration > 0) {
				$formattedDuration .= intval($duration).'s';
			}
		} elseif($duration < 0.1 && CONFIG_DURATION_UNIT == 'ms') {
			$formattedDuration = round($duration * 1000).'ms';
		} else {
			$formattedDuration = $this->formatDuration($duration, $decimals).'s';
		}
		
		return $formattedDuration;
	}
	
	function shortenQueryText($queryText) {
		if(CONFIG_MAX_QUERY_LENGTH > 0 && strlen($queryText) > CONFIG_MAX_QUERY_LENGTH) {
			return substr($queryText, 0, CONFIG_MAX_QUERY_LENGTH).'...';
		} else {
			return $queryText;
		}
	}
	
	function formatRealQuery($query, $prepend = '', $append = '') {
		return $prepend.$this->shortenQueryText($query->getText()).$append;
	}
	
	function containsReportBlock($reportBlockName) {
		$count = count($this->reportBlocks);
		for($i = 0; $i < $count; $i++) {
			if(is_a($this->reportBlocks[$i], $reportBlockName)) {
				return true;
			}
		}
		return false;
	}
}
