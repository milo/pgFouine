<?php

class PostgreSQLVacuumingDatabaseLine extends PostgreSQLVacuumLogLine {
	public $database;

	function __construct($database) {
		$this->PostgreSQLVacuumLogLine();
		
		$this->database = $database;
	}
	
	function getDatabase() {
		return $this->database;
	}
}
