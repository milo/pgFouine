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

require_once('lib/common.lib.php');
require_once('base.lib.php');

class GenericLogReader {
	public $displayHelp = true;
	
	public $fileName;
	public $lineParserName;
	public $accumulatorName;
	
	public $lineParsedCounter = 0;
	public $timeToParse;
	
	public $firstLineTimestamp;
	public $lastLineTimestamp;
	
	public $reportAggregators = array();
	public $listeners = array();
	
	function __construct($fileName, $lineParserName, $accumulatorName, $displayHelp = true) {
		$this->fileName = $fileName;
		$this->lineParserName = $lineParserName;
		$this->accumulatorName = $accumulatorName;
		
		$this->displayHelp = $displayHelp;
	}
	
	function addReportAggregator(& $reportAggregator) {
		$this->reportAggregators[] =& $reportAggregator;
	}
	
	function & getReportAggregators() {
		return $this->reportAggregators;
	}

	function parse() {
		global $lineParsedCounter;
		
		$this->prepare();
		
		$startTimestamp = time();
		
		$accumulator = new $this->accumulatorName;
		$lineParser = new $this->lineParserName;
		
		foreach(array_keys($this->listeners) AS $listenerName) {
			$listener =& $this->listeners[$listenerName];
			foreach($listener->getSubscriptions() AS $eventType) {
				$accumulator->addListener($eventType, $listener);
			}
		}
		
		if(DEBUG) {
			debug('Using parser: '.$this->lineParserName);
			debug('Using accumulator: '.$this->accumulatorName);
			debug('Using listeners: '.implode(', ', array_keys($this->listeners)));
		}
		
		$filePointer = @fopen($this->fileName, 'r');
		if(!$filePointer) {
			trigger_error('File '.$this->fileName.' is not readable.', E_USER_ERROR);
		}
		
		$lineParsedCounter = 0;
		$lineDetected = false;
		
		if(DEBUG) debug(getMemoryUsage());
		if(PROFILE) {
			$GLOBALS['profiler'] = new Profiler();
			$GLOBALS['profiler']->start();
		}
		
		$this->readFile($accumulator, $filePointer, $lineParser, $lineParsedCounter, $lineDetected);
		
		DEBUG && debug('Before close - '.getMemoryUsage());
		$accumulator->close();
		DEBUG && debug('After close - '.getMemoryUsage());
		
		fclose($filePointer);
		
		$this->timeToParse = time() - $startTimestamp;
		$this->lineParsedCounter = $lineParsedCounter;
		
		DEBUG && debug("\nParsed ".$lineParsedCounter.' lines in '.$this->timeToParse.' s');
		
		if(PROFILE) {
			$GLOBALS['profiler']->end();
			$GLOBALS['profiler']->displayProfile();
		}
		
		if(!$lineParsedCounter) {
			stderr('Log file is empty.');
			exit(0);
		}
		
		if(!$lineDetected && $this->displayHelp) {
			stderr('pgFouine did not find any valid PostgreSQL log line in your log file:');
			stderr('* check that PostgreSQL uses an english locale for logging (lc_messages in your postgresql.conf),');
			stderr('* check that you use the -logtype option (syslog, stderr) according to your log file,');
			stderr('* if you use syslog and log_line_prefix, check that your log_line_prefix has a trailing space,');
			stderr('* if you use stderr, check that your log_line_prefix is of the form \'%t [%p]: [%l-1] \'.');
			stderr('If you think your log file and your options are correct, please contact the author (gsmet on #postgresql@freenode or guillaume-pg at smet dot org).');
			exit(1);
		}
	}
	
	function readFile(& $accumulator, & $filePointer, &$lineParser, &$lineParsedCounter, &$lineDetected) {
		$currentTimestamp = time();
		
		while (!feof($filePointer)) {
			$text = rtrim(fgets($filePointer), "\r\n");
			if(empty($text)) {
				continue;
			}
			$lineParsedCounter ++;
			
			$line = $lineParser->parse($text);
			if($line) {
				if(!isset($this->firstLineTimestamp)) {
					$this->firstLineTimestamp = $line->getTimestamp();
				}
				$this->lastLineTimestamp = $line->getTimestamp();
				$accumulator->append($line);
				
				if(!is_a($line, 'PostgreSQLContinuationLine')) {
					$lineDetected = true;
				}
			}
			if($lineParsedCounter % 20000 == 0 && isset($this->lastLineTimestamp)) {
				if(DEBUG) {
					debug('    Garbage collector:');
					debug('         before: '.getMemoryUsage());
				}
				$accumulator->garbageCollect($this->lastLineTimestamp);
				if(DEBUG) {
					debug('         after: '.getMemoryUsage());
				}
			}
			if($lineParsedCounter % 100000 == 0) {
				stderr('parsed '.$lineParsedCounter.' lines');
				if(DEBUG) {
					$currentTime = time() - $currentTimestamp;
					$currentTimestamp = time();
					debug('    '.getMemoryUsage());
					debug('    Time: '.$currentTime.' s');
				}
			}
		}
	}
	
	function output() {
		for($i = 0; $i < count($this->reportAggregators); $i++) {
			$this->reportAggregators[$i]->output();
		}
	}
	
	function prepare() {
		$needs = array();
		
		for($i = 0; $i < count($this->reportAggregators); $i++) {
			$needs = array_merge($needs, $this->reportAggregators[$i]->getNeeds());
		}
		$needs = array_unique($needs);
		foreach($needs AS $need) {
			$this->addListener($need);
		}
	}
	
	function getLineParsedCounter() {
		return $this->lineParsedCounter;
	}
	
	function addListener($listenerName) {
		$listener = new $listenerName();
		$this->listeners[$listenerName] =& $listener;
	}
	
	function & getListener($listenerName) {
		if(isset($this->listeners[$listenerName])) {
			$listener =& $this->listeners[$listenerName];
		} else {
			$listener = false;
		}
		return $listener;
	}
	
	function getFileName() {
		return $this->fileName;
	}
	
	function getTimeToParse() {
		return $this->timeToParse;
	}
	
	function getLineParsedCount() {
		return $this->lineParsedCounter;	
	}
	
	function getFirstLineTimestamp() {
		return $this->firstLineTimestamp;
	}
	
	function getLastLineTimestamp() {
		return $this->lastLineTimestamp;
	}
}
