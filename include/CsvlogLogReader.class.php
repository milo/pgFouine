<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
 * Copyright (c) 2008 Guillaume Smet
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

class CsvlogLogReader extends GenericLogReader {

	function readFile(& $accumulator, & $filePointer, &$lineParser, &$lineParsedCounter, &$lineDetected) {
		$currentTimestamp = time();

		while ($csvLine = fgetcsv($filePointer)) {
			$lineParsedCounter ++;
			
			if(count($csvLine) == 22) {
				$lines =& $lineParser->parse($csvLine);
				
				if($lines) {
					if(!isset($this->firstLineTimestamp)) {
						$this->firstLineTimestamp = $lines[0]->getTimestamp();
					}
					$this->lastLineTimestamp = $lines[0]->getTimestamp();
					for($i = 0, $max = count($lines); $i < $max; $i++) {
						$accumulator->append($lines[$i]);
					}
					$lineDetected = true;
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
	}
}

?>