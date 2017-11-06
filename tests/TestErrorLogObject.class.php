<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/LogObject.class.php');
require_once('../include/ErrorLogObject.class.php');

class TestErrorLogObject extends UnitTestCase {
	
	function testInstanciation() {
		define('TEST_CONNECTION_ID', 4356);
		define('TEST_USER', 'test user');
		define('TEST_DB', 'test db');
		define('TEST_TEXT', 'test text');
		
		$errorLogObject = new ErrorLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT);
		$this->assertFalse($errorLogObject->isIgnored());
		$this->assertEqual(TEST_TEXT, $errorLogObject->getText());
		$this->assertEqual(TEST_TEXT, $errorLogObject->getError());
		$this->assertEqual(EVENT_ERROR, $errorLogObject->getEventType());
	}
	
	function testSettersAndGetters() {
		define('TEST_CONNECTION_ID', 4356);
		define('TEST_USER', 'test user');
		define('TEST_DB', 'test db');
		define('TEST_TEXT', 'test text');
		define('TEST_STATEMENT', 'test_statement');
		define('TEST_HINT', 'test_hint');
		define('TEST_DETAIL', 'test_detail');
		
		$errorLogObject = new ErrorLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT);
		$errorLogObject->appendStatement(TEST_STATEMENT);
		$this->assertEqual(TEST_STATEMENT, $errorLogObject->getText());
		
		$errorLogObject->appendHint(TEST_HINT);
		$this->assertEqual(TEST_HINT, $errorLogObject->getHint());
		
		$errorLogObject->appendDetail(TEST_DETAIL);
		$this->assertEqual(TEST_DETAIL, $errorLogObject->getDetail());
	}
}

?>