<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/postgresql/postgresql.lib.php');

define('QUERY_LINE', 'Feb 25 04:03:00 rubyforge postgres[4545]: [2755] LOG:  query: SELECT * FROM plugins');
define('QUERY_LINE_WITH_DASH_IN_CONNECTION_ID', 'Feb 25 04:03:00 rubyforge postgres[4545]: [2755-4] LOG:  query: SELECT * FROM plugins');
define('DURATION_LINE', 'May 10 07:50:42 bb postgres[24588]: [4-1] LOG:  duration: 13.968 ms');
define('RANDOM_SYSLOG_LINE', 'Mar  9 14:29:05 hal modprobe: modprobe: Can\'t locate module sound-service-1-0');

define('DETECTION_CONNECTION_RECEIVED_LINE', 'Mar 29 00:00:00 hostname postgres[28965]: [30-1] LOG:  connection received: host=192.168.2.4 port=34377');
define('DETECTION_CONNECTION_AUTHORIZED_LINE', 'Mar 29 00:00:00 hostname postgres[28965]: [31-1] LOG:  connection authorized: user=username database=databasename');
define('DETECTION_STATEMENT_LINE', 'Mar 29 10:45:50 hostname postgres[22213]: [36-2] STATEMENT:  BEGIN WORK');
define('DETECTION_ERROR_LINE', 'Mar 29 10:45:50 hostname postgres[22213]: [37-1] ERROR:  current transaction is aborted, commands ignored until end of transaction block');
define('DETECTION_QUERY_LINE_1', 'Mar 29 10:45:58 hostname postgres[22186]: [39-1] LOG:  duration: 509.852 ms  statement: SELECT * FROM mytable');
define('DETECTION_QUERY_LINE_2', 'Mar 29 11:10:25 hostname postgres[18499]: [132-1] LOG:  duration: 581.312 ms  statement: ');
define('DETECTION_CONTINUATION_LINE', 'Mar 29 10:45:58 hostname postgres[22186]: [39-2] 				FROM');
define('DETECTION_DETAIL_LINE', 'Mar 29 10:08:30 hostname postgres[18070]: [50-2] DETAIL:  Key (fieldname)=() is not present in table "tablename".');


class TestSyslogPostgreSQLParser extends UnitTestCase {
	var $parser;
	
	function setup() {
		$this->parser = new SyslogPostgreSQLParser();
	}
	
	function testPostgreSQLLineDetection() {
		$this->assertTrue($this->parser->parse(QUERY_LINE));
		$this->assertTrue($this->parser->parse(QUERY_LINE_WITH_DASH_IN_CONNECTION_ID));
		$this->assertTrue($this->parser->parse(DURATION_LINE));
		$this->assertFalse($this->parser->parse(RANDOM_SYSLOG_LINE));
	}
	
	function testConnectionId() {
		$line =& $this->parser->parse(QUERY_LINE_WITH_DASH_IN_CONNECTION_ID);
		$this->assertEqual('4545', $line->getConnectionId());
	}
	
	function testCommandNumber() {
		$line =& $this->parser->parse(QUERY_LINE);
		$this->assertEqual('2755', $line->getCommandNumber());
	}
	
	function testCommandNumberWithDash() {
		$line =& $this->parser->parse(QUERY_LINE_WITH_DASH_IN_CONNECTION_ID);
		$this->assertEqual('2755', $line->getCommandNumber());
	}
	
	function testLineNumber() {
		$line =& $this->parser->parse(QUERY_LINE);
		$this->assertEqual('1', $line->getLineNumber());
	}
	
	function testLineNumberWithDash() {
		$line =& $this->parser->parse(QUERY_LINE_WITH_DASH_IN_CONNECTION_ID);
		$this->assertEqual('4', $line->getLineNumber());
	}
	
	function testQueryLineDetection() {
		$line =& $this->parser->parse(DETECTION_QUERY_LINE_1);
		$this->assertTrue(is_a($line, 'PostgreSQLQueryStartWithDurationLine'));
		
		$line =& $this->parser->parse(DETECTION_QUERY_LINE_2);
		$this->assertTrue(is_a($line, 'PostgreSQLQueryStartWithDurationLine'));
	}
	
	function testContinuationLineDetection() {
		$line =& $this->parser->parse(DETECTION_CONTINUATION_LINE);
		$this->assertTrue(is_a($line, 'PostgreSQLContinuationLine'));
	}
	
	function testStatementLineDetection() {
		$line =& $this->parser->parse(DETECTION_STATEMENT_LINE);
		$this->assertTrue(is_a($line, 'PostgreSQLStatementLine'));
	}
	
	function testStatusLineDetection() {
		$line =& $this->parser->parse(DETECTION_CONNECTION_RECEIVED_LINE);
		$this->assertTrue(is_a($line, 'PostgreSQLStatusLine'));
		
		$line =& $this->parser->parse(DETECTION_CONNECTION_AUTHORIZED_LINE);
		$this->assertTrue(is_a($line, 'PostgreSQLStatusLine'));
	}
	
	function testErrorLineDetection() {
		$line =& $this->parser->parse(DETECTION_ERROR_LINE);
		$this->assertTrue(is_a($line, 'PostgreSQLErrorLine'));
	}
	
	function testDetailLineDetection() {
		$line =& $this->parser->parse(DETECTION_DETAIL_LINE);
		$this->assertTrue(is_a($line, 'PostgreSQLDetailLine'));
	}
}

?>