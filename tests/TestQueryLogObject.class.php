<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/LogObject.class.php');
require_once('../include/QueryLogObject.class.php');

class TestQueryLogObject extends UnitTestCase {
	
	function testInstanciation() {
		define('TEST_CONNECTION_ID', 4356);
		define('TEST_USER', 'test user');
		define('TEST_DB', 'test db');
		define('TEST_TEXT', 'test text');
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT);
		$this->assertEqual(EVENT_QUERY, $query->getEventType());
		$this->assertFalse($query->isIgnored());
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT, true);
		$this->assertTrue($query->isIgnored());
		$this->assertEqual(TEST_TEXT, $query->getText());
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT, false);
		$this->assertFalse($query->isIgnored());
	}
	
	function testSettersAndGetters() {
		define('TEST_TEXT', 'test text');
		define('TEST_DB', 'test_db');
		define('TEST_USER', 'test_user');
		define('TEST_DURATION', 100);
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT);

		$query->setDuration(TEST_DURATION);
		$this->assertEqual(TEST_DURATION, $query->getDuration());
	}
	
	function testTypeDetection() {
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, 'select * from mytable');
		$this->assertTrue($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, 'SELECT * FROM mytable');
		$this->assertTrue($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, 'delete from mytable');
		$this->assertFalse($query->isSelect());
		$this->assertTrue($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, 'DELETE FROM mytable');
		$this->assertFalse($query->isSelect());
		$this->assertTrue($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, 'insert into mytable values(4)');
		$this->assertFalse($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertTrue($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, 'INSERT INTO mytable VALUES(4)');
		$this->assertFalse($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertTrue($query->isInsert());
		$this->assertFalse($query->isUpdate());
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, 'update mytable set field=4');
		$this->assertFalse($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertTrue($query->isUpdate());
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, 'UPDATE mytable SET field=4');
		$this->assertFalse($query->isSelect());
		$this->assertFalse($query->isDelete());
		$this->assertFalse($query->isInsert());
		$this->assertTrue($query->isUpdate());
	}
	
	function testSubQuery() {
		define('TEST_TEXT1', 'test text 1');
		define('TEST_TEXT2', 'test text 2');
		define('TEST_TEXT3', 'test text 3');
		define('TEST_TEXT4', 'test text 4');
		
		$query = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT1);
		
		$query2 = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT2);
		$query3 = new QueryLogObject(TEST_CONNECTION_ID, TEST_USER, TEST_DB, TEST_TEXT3);
		
		$query->addSubQuery($query2);
		$subQueries = $query->getSubQueries();
		
		$this->assertEqual(1, count($subQueries));
		$this->assertReference($query2, $subQueries[0]);
		
		$query->addSubQuery($query3);
		$subQueries = $query->getSubQueries();
		
		$this->assertEqual(2, count($subQueries));
		$this->assertReference($query2, $subQueries[0]);
		$this->assertReference($query3, $subQueries[1]);
	}
}

?>