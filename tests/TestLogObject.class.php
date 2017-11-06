<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/LogObject.class.php');

class TestLogObject extends UnitTestCase {
	
	function testInstanciation() {
		define('TEST_CONNECTION_ID', 4356);
		define('TEST_TEXT', 'test text');
		define('TEST_DB', 'test_db');
		define('TEST_USER', 'test_user');
		
		$logObject = new LogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT);
		$this->assertFalse($logObject->isIgnored());
		
		$logObject = new LogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT, true);
		$this->assertTrue($logObject->isIgnored());
		$this->assertEqual(TEST_TEXT, $logObject->getText());
		
		$logObject = new LogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT, false);
		$this->assertFalse($logObject->isIgnored());
	}
	
	
	function testSettersAndGetters() {
		define('TEST_CONNECTION_ID', 4356);
		define('TEST_TEXT', 'test text');
		define('TEST_DB', 'test_db');
		define('TEST_USER', 'test_user');
		define('TEST_TIMESTAMP', 1234567890);
		define('TEST_COMMAND_NUMBER', 43);
		
		$logObject = new LogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT);

		$this->assertEqual(TEST_CONNECTION_ID, $logObject->getConnectionId());
		$this->assertEqual(TEST_DB, $logObject->getDatabase());
		$this->assertEqual(TEST_USER, $logObject->getUser());
		
		$logObject->setContextInformation(TEST_TIMESTAMP, TEST_COMMAND_NUMBER);
		$this->assertEqual(TEST_TIMESTAMP, $logObject->getTimestamp());
		$this->assertEqual(TEST_COMMAND_NUMBER, $logObject->getCommandNumber());
    }

	function testNormalize() {
		$testQuery = "SELECT * FROM   mytable WHERE field1=4 AND field2='string' AND field3=0x80 AND field4 IN ('test',   5, 0x80 ) AND field5 IN (SELECT 1 FROM test)";
		$logObject = new LogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, $testQuery, false);
		$this->assertEqual($testQuery, $logObject->getText());
		$this->assertEqual("SELECT * FROM mytable WHERE field1=0 AND field2='' AND field3=0x AND field4 IN (...) AND field5 IN (SELECT 0 FROM test)", $logObject->getNormalizedText());
	}
	
	function testAppend() {
		define('TEST_TEXT1', 'test text 1');
		define('TEST_TEXT2', 'test text 2');
		
		$logObject = new LogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT1);
		$logObject->append(TEST_TEXT2);
		$this->assertEqual(TEST_TEXT1.' '.TEST_TEXT2, $logObject->getText());
	}
}
