<?php

/*
 * This file is part of pgFouine.
 * 
 * pgFouine - a PostgreSQL log analyzer
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

class TsungSessionsReport extends Report {
	function __construct(& $reportAggregator) {
		parent::__construct($reportAggregator, 'Tsung sessions', ['TsungSessionsListener'], false);
	}
	
	function getText() {
		$listener = $this->reportAggregator->getListener('TsungSessionsListener');
		$sessions = $listener->getSessions();
		$sessionsCount = count($sessions);
		$probabilityLeft = 100;
		
		$text = '';
		$text .= '<sessions>'."\n";
		
		for($i = 0; $i < $sessionsCount; $i++) {
			if($i == ($sessionsCount - 1)) {
				$currentProbability = $probabilityLeft;
			} else {
				$currentProbability = (int) (100 / $sessionsCount);
				$probabilityLeft -= $currentProbability;
			}
			
			$connectionId = key($sessions);
			$queries = current($sessions);
			$queriesCount = count($queries);
			$text .= "\t".'<session name="pgfouine-'.$connectionId.'" probability="'.$currentProbability.'" type="ts_pgsql">'."\n";
			
			for($j = 0; $j < $queriesCount; $j++) {
				$query =& $queries[$j];
				if($j == 0) {
					$text .= "\t\t".'<request><pgsql type="connect" database="'.$query->getDatabase().'" username="'.$query->getUser().'" /></request>'."\n";
				}
				if(isset($lastQuery)) {
					$thinkTime = (int) ($query->getTimestamp() - ($lastQuery->getTimestamp() + $lastQuery->getDuration()));
					if($thinkTime >= 1) {
						$text .= "\t\t".'<thinktime random="true" value="'.$thinkTime.'" />'."\n";
					}
					unset($lastQuery);
				}
				$text .= "\t\t".'<request><pgsql type="sql"><![CDATA['.$query->getText().']]></pgsql></request>'."\n";
				
				$lastQuery =& $query;
				unset($query);
			}
			
			$text .= "\t".'</session>'."\n";
			next($sessions);
		}
		
		$text .= '</sessions>'."\n";

		return $text;
	}
	
	function getHtml() {
		$html = '<p>Report not supported by HTML format</p>';
		
		return $html;
	}
}
