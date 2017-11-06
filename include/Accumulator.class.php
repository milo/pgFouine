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
 * the accumulator is the central point of the analyzer
 * 
 * - we append the lines to the right log streams via the accumulator
 * - when a log object is identified, the accumulator fires the event
 * to all the listeners
 */
class Accumulator {
	public $listeners = [];
	
	function append(& $line) {
	}
	
	function addListener($eventType, & $listener) {
		$this->listeners[$eventType][] =& $listener;
	}
	
	function fireEvent(& $logObject) {
		$listeners =& $this->listeners[$logObject->getEventType()];
		$countListeners = count($listeners);
		for($i = 0; $i < $countListeners; $i++) {
			$listener =& $listeners[$i];
			$listener->fireEvent($logObject);
			unset($listener);
		}
	}
	
	/**
	 * close the accumulator by:
	 * - flushing the log streams
	 * - closing all the existing listeners
	 */
	function close() {
		$this->flushLogStreams();
		$this->closeListeners();
	}
	
	/**
	 * flush the log streams
	 */
	function flushLogStreams() {
	}
	
	/**
	 * run the garbage collector
	 */
	function garbageCollect($lastLineTimestamp) {
	}
	
	/**
	 * closes all the listeners
	 */
	function closeListeners() {
		$eventTypes = array_keys($this->listeners);
		foreach($eventTypes AS $eventType) {
			$listenerCount = count($this->listeners[$eventType]);
			for($i = 0; $i < $listenerCount; $i++) {
				$listener =& $this->listeners[$eventType][$i];
				$listener->close();
				unset($listener);
			}
		}
	}
}
