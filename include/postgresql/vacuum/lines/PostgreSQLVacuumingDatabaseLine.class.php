<?php

class PostgreSQLVacuumingDatabaseLine extends PostgreSQLVacuumLogLine {
	var $database;

	function PostgreSQLVacuumingDatabaseLine($database) {
		$this->PostgreSQLVacuumLogLine();
		
		$this->database = $database;
	}
	
	function getDatabase() {
		return $this->database;
	}
}

?>