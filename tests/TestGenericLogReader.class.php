<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/GenericLogReader.class.php');

class TestGenericLogReader extends UnitTestCase {
	
	function testReadLogFile() {
		$logReader = new GenericLogReader(
			'logs/TestGenericLogReader/testReadLogFile.log',
			'Parser',
			'Accumulator',
			false
		);
		$logReader->parse();
		
		$this->assertEqual(4, $logReader->getLineParsedCounter());
	}
	
}

?>