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

class NormalizedError {
	public $normalizedText;
	public $error = '';
	public $hint = '';
	public $detail = '';
	public $textIsAStatement = false;
	public $examples = [];
	public $count = 0;
	public $hourlyStatistics = [];
	
	function NormalizedError(& $error) {
		$this->normalizedText = $error->getNormalizedText();
		$this->error = $error->getError();
		$this->hint = $error->getHint();
		$this->detail = $error->getDetail();
		$this->textIsAStatement = $error->isTextAStatement();
		
		$this->addError($error);
	}
	
	function addError(& $error) {
		$this->count ++;
		if(count($this->examples) < CONFIG_MAX_NUMBER_OF_EXAMPLES) {
			$this->examples[] =& $error;
		}
		$formattedTimestamp = date('Y-m-d H:00:00', $error->getTimestamp());
		if(!isset($this->hourlyStatistics[$formattedTimestamp])) {
			$this->hourlyStatistics[$formattedTimestamp]['count'] = 0;
		}
		$this->hourlyStatistics[$formattedTimestamp]['count']++;
	}
	
	function getNormalizedText() {
		return $this->normalizedText;
	}
	
	function getError() {
		return $this->error;
	}
	
	function getTimesExecuted() {
		return $this->count;
	}
	
	function & getFilteredExamplesArray() {
		$examples = [];
		$exampleCount = count($this->examples);
		
		for($i = 0; $i < $exampleCount; $i++) {
			$example =& $this->examples[$i];
			if($example->getText() != $this->getNormalizedText()) {
				$examples =& $this->examples;
				break;
			}
			unset($example);
		}
		
		return $examples;
	}
	
	function getDetail() {
		return $this->detail;
	}
	
	function getHint() {
		return $this->hint;
	}
	
	function isTextAStatement() {
		return $this->textIsAStatement;
	}
	
	function & getHourlyStatistics() {
		ksort($this->hourlyStatistics);
		return $this->hourlyStatistics;
	}
}
