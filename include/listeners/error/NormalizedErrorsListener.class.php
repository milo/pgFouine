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

class NormalizedErrorsListener extends ErrorListener {
	public $errorsList = [];
	public $errorsNumber = 10;
	
	function __construct() {
		$this->errorsNumber = CONFIG_TOP_QUERIES_NUMBER;
	}
	
	function fireEvent(& $error) {
		$normalizedText = $error->getNormalizedText();
		if(isset($this->errorsList[$normalizedText])) {
			$this->errorsList[$normalizedText]->addError($error);
		} else {
			$this->errorsList[$normalizedText] = new NormalizedError($error);
		}
	}
	
	function & getMostFrequentErrors() {
		$errorsList = $this->errorsList;
		usort($errorsList, [$this, 'compareMostFrequent']);
		$errors = array_slice($errorsList, 0, $this->errorsNumber);
		return $errors;
	}
	
	function compareMostFrequent(& $a, & $b) {
		if($a->getTimesExecuted() == $b->getTimesExecuted()) {
			return 0;
		} elseif($a->getTimesExecuted() < $b->getTimesExecuted()) {
			return 1;
		} else {
			return -1;
		}
	}
	
	function getUniqueErrorCount() {
		return count($this->errorsList);
	}
}
