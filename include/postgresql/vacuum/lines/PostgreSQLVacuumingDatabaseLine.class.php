<?php

class PostgreSQLVacuumingDatabaseLine extends PostgreSQLVacuumLogLine {
	public $database;

	function __construct($database) {
		parent::__construct();
		
		$this->database = $database;
	}
	
	function getDatabase() {
		return $this->database;
	}
}
