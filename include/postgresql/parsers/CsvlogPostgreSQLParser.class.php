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

class CsvlogPostgreSQLParser extends PostgreSQLParser {
	
	function CsvlogPostgreSQLParser() {
	}

	function & parse($data) {
		// TODO: problem with notice before a statement
		
		$timestamp = strtotime($data[0]);
		// TODO: check database and user
		$user = $data[1];
		$database = $data[2];
		$connectionId = $data[3];
		// $data[4] = connection from
		// TODO: check command number
		$commandNumber = $data[5].'-'.$data[6];
		$lineNumber = 1;
		// $data[7] = command tag
		// $data[8] = session start timestamp
		// $data[9] = virtual transaction id
		// $data[10] = transaction id
		$keyword = $data[11]; // LOG, ERROR, WARNING...
		// $data[12] = sql state
		$message = $data[13];
		$detail = $data[14];
		$hint = $data[15];
		$internalQuery = $data[16];
		$context = $data[18];
		$statement = $data[19];
		
		$line =& parent::parse($keyword.': '.$message);
		
		$lines = array();
		if($line) {
			$lines[] =& $line;
			
			if($detail) {
				$lines[] = new PostgreSQLDetailLine($detail);
			}
			if($hint) {
				$lines[] = new PostgreSQLHintLine($hint);
			}
			if($context) {
				$lines[] = new PostgreSQLContextLine($context);
			}
			if($statement) {
				$lines[] = new PostgreSQLStatementLine($statement);
			}
			// TODO: internal query?
			// TODO: prepared statements, stored procedures...
		}
		for($i = 0, $max = count($lines); $i < $max; $i++) {
			$currentLine =& $lines[$i];
			$currentLine->setConnectionInformation($database, $user);
			$currentLine->setContextInformation($timestamp, $connectionId, $commandNumber, $i + 1);
			unset($currentLine);
		}

		return $lines;
	}
}
