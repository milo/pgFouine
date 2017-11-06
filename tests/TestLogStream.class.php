<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/LogStream.class.php');
require_once('../include/QueryLogObject.class.php');

define('LOG_STREAM_HOST', 'test_host');
define('LOG_STREAM_PORT', '30123');
define('LOG_STREAM_USER', 'test_user');
define('LOG_STREAM_DB', 'test_db');

class TestLogStream extends UnitTestCase {
	
	function testSetHostConnection() {
		$logStream = new LogStream();
		$logStream->setHostConnection(LOG_STREAM_HOST, LOG_STREAM_PORT);
		$this->assertEqual(LOG_STREAM_HOST, $logStream->getHost());
		$this->assertEqual(LOG_STREAM_PORT, $logStream->getPort());
	}
	
	function testSetUserDb() {
		$logStream = new LogStream();
		$logStream->setUserDatabase(LOG_STREAM_USER, LOG_STREAM_DB);
		$this->assertEqual(LOG_STREAM_USER, $logStream->getUser());
		$this->assertEqual(LOG_STREAM_DB, $logStream->getDatabase());
	}
}

?>