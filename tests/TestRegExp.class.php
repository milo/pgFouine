<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/lib/common.lib.php');

class TestRegExp extends UnitTestCase {
	
	function testPattern() {
		define('TEST_PATTERN', 'test pattern');
		$regexp = new RegExp(TEST_PATTERN);
		$this->assertEqual(TEST_PATTERN, $regexp->getPattern());
	}
	
	function testPatternDetection() {
		define('TEST_DETECTION_PATTERN', '/test/i');
		define('TEST_DETECTION_STRING', 'this is a TEST string');
		
		$regexp = new RegExp(TEST_DETECTION_PATTERN);
		$this->assertTrue($regexp->match(TEST_DETECTION_STRING));
	}
	
	function testMatch() {
		define('TEST_MATCH_PATTERN', '/t(es)t/i');
		define('TEST_MATCH_STRING', 'this is a TEST string');
		
		$regexp = new RegExp(TEST_MATCH_PATTERN);
		$regexpMatch =& $regexp->match(TEST_MATCH_STRING);
		
		$this->assertEqual('TEST', $regexpMatch->getMatch(0));
		$this->assertEqual('ES', $regexpMatch->getMatch(1));
		
		$this->assertEqual(' string', $regexpMatch->getPostMatch());
	}
}

?>