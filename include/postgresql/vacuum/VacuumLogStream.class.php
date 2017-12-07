<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2006 Open Wide
 * Copyright (c) 2006-2008 Guillaume Smet
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
class VacuumLogStream {
	public $currentBlock = false;
	public $database = UNKNOWN_DATABASE;

	/**
	 * append a log line to the log stream
	 * this method is complicated as there are a lot of special cases to take into account
	 * 
	 * @param object $line log line
	 */
	function append(& $line) {
		$logObject = false;
		if(is_a($line, 'PostgreSQLVacuumingTableLine')
			|| is_a($line, 'PostgreSQLAnalyzingTableLine')
			|| is_a($line, 'PostgreSQLFSMInformationLine')
		) {
			if($this->currentBlock) {
				$logObject = $this->currentBlock->close();
			}
			$this->currentBlock = new LogBlock($this, $line->getLineNumber(), $line);
		} elseif(is_a($line, 'PostgreSQLVacuumingDatabaseLine')) {
			$this->database = $line->getDatabase();
		} elseif(is_a($line, 'PostgreSQLVacuumEndLine')) {
			if($this->currentBlock) {
				$logObject = $this->currentBlock->close();
			}
		} elseif($this->currentBlock) {
			if(is_a($line, 'PostgreSQLVacuumContinuationLine')) {
				// it is just a continuation line so we just add the text to the text of the last line)
				if($line->getText()) {
					$lastLine = last($this->currentBlock->getLines());
					$lastLine->appendText($line->getText());
				}
			} else {
				$this->currentBlock->addLine($line);
			}
		}
		return $logObject;
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
			$logObject = $this->currentBlock->close();
			if($logObject) {
				$logObject->accumulateTo($accumulator);
			}
		}
		$this->currentBlock = false;
	}
}
