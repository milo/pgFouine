<?php

require_once('simpletest/unit_tester.php');
require_once('simpletest/reporter.php');

require_once('../include/lib/Profiler.class.php');

class TestProfiler extends UnitTestCase {
	
	function testGetTime() {
		$this->assertTrue(is_float(getTime(microtime())));
	}
	
	function testStage() {
		define('TEST_PROFILER_STAGE', 'stage1');
		
		$profiler = new Profiler();
		$profiler->start();
		
		$profiler->startStage(TEST_PROFILER_STAGE);
		sleep(1);
		$profiler->endStage(TEST_PROFILER_STAGE);
		
		$profiler->end();
		$stages = $profiler->getStages();
		$this->assertTrue(isset($stages[TEST_PROFILER_STAGE]));
		$this->assertEqual(1, $stages[TEST_PROFILER_STAGE]['count']);
		$this->assertTrue($stages[TEST_PROFILER_STAGE]['duration'] > 1);
		$this->assertTrue($stages[TEST_PROFILER_STAGE]['duration'] < 2);
	}
	
	function testTwoStages() {
		define('TEST_PROFILER_STAGE_1', 'stage1');
		define('TEST_PROFILER_STAGE_2', 'stage2');
		
		$profiler = new Profiler();
		$profiler->start();
		
		$profiler->startStage(TEST_PROFILER_STAGE_1);
		sleep(1);
		$profiler->endStage(TEST_PROFILER_STAGE_1);
		
		$profiler->startStage(TEST_PROFILER_STAGE_2);
		sleep(1);
		$profiler->endStage(TEST_PROFILER_STAGE_2);
		
		$profiler->end();
		$stages = $profiler->getStages();
		$this->assertTrue(isset($stages[TEST_PROFILER_STAGE_1]));
		$this->assertEqual(1, $stages[TEST_PROFILER_STAGE_1]['count']);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1]['duration'] > 1);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1]['duration'] < 2);
		
		$this->assertTrue(isset($stages[TEST_PROFILER_STAGE_2]));
		$this->assertEqual(1, $stages[TEST_PROFILER_STAGE_2]['count']);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_2]['duration'] > 1);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_2]['duration'] < 2);
	}
	
	function testTwoIdenticalStages() {
		define('TEST_PROFILER_STAGE_1', 'stage1');
		
		$profiler = new Profiler();
		$profiler->start();
		
		$profiler->startStage(TEST_PROFILER_STAGE_1);
		sleep(1);
		$profiler->endStage(TEST_PROFILER_STAGE_1);
		
		$profiler->startStage(TEST_PROFILER_STAGE_1);
		sleep(1);
		$profiler->endStage(TEST_PROFILER_STAGE_1);
		
		$profiler->end();
		$stages = $profiler->getStages();
		$this->assertTrue(isset($stages[TEST_PROFILER_STAGE_1]));
		$this->assertEqual(2, $stages[TEST_PROFILER_STAGE_1]['count']);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1]['duration'] > 2);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1]['duration'] < 3);
	}
	
	function testInnerStages() {
		define('TEST_PROFILER_STAGE_1', 'stage1');
		define('TEST_PROFILER_STAGE_2', 'stage2');
		define('TEST_PROFILER_STAGE_3', 'stage3');
		define('TEST_PROFILER_STAGE_4', 'stage4');
		
		$profiler = new Profiler();
		$profiler->start();
		
		$profiler->startStage(TEST_PROFILER_STAGE_1);
		sleep(1);
			$profiler->startStage(TEST_PROFILER_STAGE_2);
			sleep(1);
			$profiler->endStage(TEST_PROFILER_STAGE_2);
			
			$profiler->startStage(TEST_PROFILER_STAGE_3);
			sleep(1);
				$profiler->startStage(TEST_PROFILER_STAGE_4);
				sleep(1);
				$profiler->endStage(TEST_PROFILER_STAGE_4);
			$profiler->endStage(TEST_PROFILER_STAGE_3);
			
			$profiler->startStage(TEST_PROFILER_STAGE_2);
			sleep(1);
			$profiler->endStage(TEST_PROFILER_STAGE_2);
		$profiler->endStage(TEST_PROFILER_STAGE_1);
		
		$profiler->startStage(TEST_PROFILER_STAGE_1);
		sleep(1);
		$profiler->endStage(TEST_PROFILER_STAGE_1);
		
		$profiler->end();
		$stages = $profiler->getStages();
		$this->assertTrue(isset($stages[TEST_PROFILER_STAGE_1]));
		$this->assertEqual(2, $stages[TEST_PROFILER_STAGE_1]['count']);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1]['duration'] > 6);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1]['duration'] < 7);
		
		$this->assertTrue(isset($stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_2]));
		$this->assertEqual(2, $stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_2]['count']);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_2]['duration'] > 2);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_2]['duration'] < 3);
		
		$this->assertTrue(isset($stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_3]));
		$this->assertEqual(1, $stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_3]['count']);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_3]['duration'] > 2);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_3]['duration'] < 3);
		
		$this->assertTrue(isset($stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_3.'>'.TEST_PROFILER_STAGE_4]));
		$this->assertEqual(1, $stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_3.'>'.TEST_PROFILER_STAGE_4]['count']);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_3.'>'.TEST_PROFILER_STAGE_4]['duration'] > 1);
		$this->assertTrue($stages[TEST_PROFILER_STAGE_1.'>'.TEST_PROFILER_STAGE_3.'>'.TEST_PROFILER_STAGE_4]['duration'] < 2);
	}
	
	function testTags() {
		define('TEST_PROFILER_STAGE_1', 'stage1');
		define('TEST_PROFILER_STAGE_2', 'stage2');
		define('TEST_PROFILER_STAGE_3', 'stage3');
		define('TEST_PROFILER_STAGE_4', 'stage4');
		
		define('TEST_PROFILER_TAG_1', 'tag1');
		define('TEST_PROFILER_TAG_2', 'tag2');
		
		$profiler = new Profiler();
		$profiler->start();
		
		$profiler->startStage(TEST_PROFILER_STAGE_1);
		sleep(1);
			$profiler->startStage(TEST_PROFILER_STAGE_2);
			sleep(1);
			$profiler->endStage(TEST_PROFILER_STAGE_2, TEST_PROFILER_TAG_1);
			
			$profiler->startStage(TEST_PROFILER_STAGE_3);
			sleep(1);
				$profiler->startStage(TEST_PROFILER_STAGE_4);
				sleep(1);
				$profiler->endStage(TEST_PROFILER_STAGE_4);
			$profiler->endStage(TEST_PROFILER_STAGE_3, TEST_PROFILER_TAG_1);
			
			$profiler->startStage(TEST_PROFILER_STAGE_2);
			sleep(1);
			$profiler->endStage(TEST_PROFILER_STAGE_2, TEST_PROFILER_TAG_2);
		$profiler->endStage(TEST_PROFILER_STAGE_1);
		
		$profiler->startStage(TEST_PROFILER_STAGE_1);
		sleep(1);
		$profiler->endStage(TEST_PROFILER_STAGE_1);
		
		$profiler->end();
		$tags = $profiler->getTags();
		
		$this->assertTrue(isset($tags[TEST_PROFILER_TAG_1]));
		$this->assertEqual(2, $tags[TEST_PROFILER_TAG_1]['count']);
		$this->assertTrue($tags[TEST_PROFILER_TAG_1]['duration'] > 3);
		$this->assertTrue($tags[TEST_PROFILER_TAG_1]['duration'] < 4);
		
		$this->assertTrue(isset($tags[TEST_PROFILER_TAG_2]));
		$this->assertEqual(1, $tags[TEST_PROFILER_TAG_2]['count']);
		$this->assertTrue($tags[TEST_PROFILER_TAG_2]['duration'] > 1);
		$this->assertTrue($tags[TEST_PROFILER_TAG_2]['duration'] < 2);
	}
}

?>