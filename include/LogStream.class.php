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
 * a log stream is used as a way to separate lines coming from different backends
 * 
 * a log stream is associated to one and only one PID
 */
class LogStream {
	var $currentBlock = false;
	var $host = '';
	var $port = '';
	var $user = '';
	var $database = '';
	var $preparedStatements = array();
	var $lastLineTimestamp = 0;

	/**
	 * append a log line to the log stream
	 * this method is complicated as there are a lot of special cases to take into account
	 * 
	 * @param object $line log line
	 */	
	function append(& $line) {
		$logObject = false;
		$lineCommandNumber = $line->getCommandNumber();
		
		$this->lastLineTimestamp = $line->getTimestamp();
		
		if((!$this->currentBlock ||
			((($lineCommandNumber != $this->currentBlock->getCommandNumber()) || ($line->getLineNumber() == 1)) && $this->currentBlock->isComplete()) ||			
			is_a($line, 'PostgreSQLErrorLine'))
				&& !$line->isContextual()
		) {
			// if one of this condition is true:
			// 1. we don't have a current block (e.g. we just started a new log stream)
			// 2. the command number of the added line is different from the block command number
			// 3. the line number of the added line is 1 and the current block is complete
			// 4. the added line is an error line: it's an independant object
			// we will try to open a new log block
			if($this->currentBlock) {
				// if we have an existing log block AND if the block is declared complete, we probably want to close it
				if(is_a($line, 'PostgreSQLQueryStartWithDurationLine')
					&& $this->currentBlock->getLineCount() == 1 && ($firstLine =& $this->currentBlock->getFirstLine())
					&& is_a($firstLine, 'PostgreSQLDurationLine')
					&& $firstLine->getDuration() == $line->getDuration()) {
					// if we have a duration line with the same duration than the current query with duration, it's because log_duration and log_min_duration_statement
					// are enabled at the same time so we have both a duration line and a query with duration line for the same query.
					// we ignore this block (the duration from log_duration) and we only consider the following one (from log_min_duration_statement)
				} elseif($this->currentBlock->isComplete()) {
					// we close the block and get the associated log object
					$logObject =& $this->currentBlock->close();
				}
			}
			if($line->getLineNumber() == 1) {
				// if the line number of the added line is 1, we begin a new log block
				$this->currentBlock = new LogBlock($this, $lineCommandNumber, $line);
			} else {
				// otherwise our log file probably begins with an incomplete block
				// we only raise an error in DEBUG mode as it is very common and is not a blocking problem
				if(DEBUG) {
					stderr('we just closed a LogBlock, line number should be 1 and is '.$line->getLineNumber(), true);
					stderr('line command number: '.$lineCommandNumber);
					if($this->currentBlock) {
						stderr('current block command number: '.$this->currentBlock->getCommandNumber());
					}
					$this->currentBlock = false;
				}
			}
		} elseif($this->currentBlock) {
			// we add all the lines associated with the block to the current block
			if(is_a($line, 'PostgreSQLContinuationLine')) {
				// it is just a continuation line so we just add the text to the text of the last line)
				if($line->getText()) {
					$lastLine =& last($this->currentBlock->getLines());
					$lastLine->appendText($line->getText());
				}	
			} else {
				$this->currentBlock->addLine($line);
			}
		}
		return $logObject;
	}

	/**
	 * defines the parameters of the connection
	 * 
	 * @param string $host hostname
	 * @param string $port port used to connect to the database
	 */
	function setHostConnection($host, $port) {
		$this->host = $host;
		$this->port = $port;
	}

	/**
	 * defines the account information used to log in to the database
	 * 
	 * @param string $user user
	 * @param string $database database
	 */
	function setUserDatabase($user, $database) {
		$this->user = $user;
		$this->database = $database;
	}
	
	/**
	 * returns the host name used to connect to the database
	 * 
	 * @return string host name
	 */
	function getHost() {
		return $this->host;
	}
	
	/**
	 * returns the port used to connect to the database
	 * 
	 * @return string port
	 */
	function getPort() {
		return $this->port;
	}
	
	/**
	 * returns the user used to connect to the database
	 * 
	 * @return string user
	 */
	function getUser() {
		return $this->user;
	}
	
	/**
	 * returns the database used
	 * 
	 * @return string database
	 */
	function getDatabase() {
		return $this->database;
	}
	
	/**
	 * flushes the potential remaining log block (usually the last one before the connection
	 * was closed)
	 * 
	 * @param object $accumulator current accumulator
	 */
	function flush(& $accumulator) {
		if($this->currentBlock && $this->currentBlock->isComplete()) {
			$logObject =& $this->currentBlock->close();
			if($logObject) {
				$logObject->accumulateTo($accumulator);
			}
		}
		$this->currentBlock = false;
	}
	
	/**
	 * add a prepared statement to the list
	 * 
	 * @param object $preparedStatement the PreparedStatement object to add
	 */
	function addPreparedStatement(& $preparedStatement) {
		$name = $preparedStatement->getName();
		$this->preparedStatements[$name] =& $preparedStatement;
	}
	
	/**
	 * returns a prepared statement by name
	 * 
	 * @param string $name name of the prepared statement
	 * @param string $portalName name of the portal
	 */
	function & getPreparedStatement($name, $portalName) {
		return $this->preparedStatements[$name];
	}
	
	function getLastLineTimestamp() {
		return $this->lastLineTimestamp;
	}
}

?>