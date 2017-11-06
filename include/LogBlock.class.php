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

/**
 * A log block is a set of lines we identified as belonging to the
 * same PostgreSQL log object (a statement, an error...).
 * 
 * When a log block is complete, we can parse it to build a log object.
 * The completion of a node is decided by the line objects we add in this
 * block.
 * 
 * Even if we have a command number in the log block, you can have lines with different
 * command numbers in the same log block. PostgreSQL does not log all the lines associated
 * with a log object with the same command number (for example, when you use log_statement
 * and log_duration, the statement text and the duration are logged with different command
 * numbers).
 */
class LogBlock {
	var $logStream;
	var $commandNumber;
	var $lines = array();
	var $complete = false;
	var $lastLineNumber = 0;
	
	function LogBlock(& $logStream, $commandNumber, & $line) {
		$this->logStream =& $logStream;
		$this->commandNumber = $commandNumber;
		$this->addLine($line);
	}
	
	/**
	 * returns the command number currently associated with the log block
	 * 
	 * @return int command number
	 */
	function getCommandNumber() {
		return $this->commandNumber;
	}
	
	/**
	 * returns the line number associated with the last line added to the block
	 * 
	 * @return int line number
	 */
	function getLastLineNumber() {
		return $this->lastLineNumber;
	}
	
	/**
	 * returns an array containing all the log lines added to the block
	 * 
	 * @return array lines
	 */
	function & getLines() {
		return $this->lines;
	}
	
	/**
	 * returns the number of lines added
	 * 
	 * @return int count
	 */
	function getLineCount() {
		return count($this->lines);
	}
	
	/**
	 * returns the first line added to the block or false if the block is empty
	 * 
	 * @return mixed first log line
	 */
	function & getFirstLine() {
		if(isset($this->lines[0])) {
			$line =& $this->lines[0];
		} else {
			$line = false;
		}
		return $line;
	}
	
	/**
	 * adds a log line to our block. If the line is a candidate to complete the log
	 * block, it declares the log block as complete.
	 * 
	 * @param object $line the line to add
	 */
	function addLine(& $line) {
		$this->complete = $this->complete || $line->complete();
		$this->lastLineNumber = $line->getLineNumber();
		$this->lines[] =& $line;
	}
	
	/**
	 * returns true if the log block is declared complete
	 * 
	 * @return boolean true if complete
	 */
	function isComplete() {
		return $this->complete;
	}

	/**
	 * closes the log block and builds a log object from the lines previously added
	 * to the block
	 * 
	 * @return object log object
	 */	
	function & close() {
		$count = count($this->lines);
		$logObject =& $this->lines[0]->getLogObject($this->logStream);
			
		if($logObject && !$logObject->isIgnored()) {
			for($i = 1; $i < $count; $i++) {
				$this->lines[$i]->appendTo($logObject);
			}
		}
		if(is_a($logObject, 'UselessLogObject')) {
			unset($logObject);
			$logObject = false;
		}
		return $logObject;
	}
}
