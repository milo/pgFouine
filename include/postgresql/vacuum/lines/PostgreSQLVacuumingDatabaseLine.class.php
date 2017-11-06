<?php

class PostgreSQLVacuumingDatabaseLine extends PostgreSQLVacuumLogLine {
	public $database;

	function PostgreSQLVacuumingDatabaseLine($database) {
		$this->PostgreSQLVacuumLogLine();
		
		$this->database = $database;
	}
	
	function getDatabase() {
		return $this->database;
	}
}
