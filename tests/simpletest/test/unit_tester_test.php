<?php
    // $Id: unit_tester_test.php,v 1.1 2005/11/09 23:41:18 gsmet Exp $
    
    class TestOfUnitTester extends UnitTestCase {
        
        function testAssertTrueReturnsAssertionAsBoolean() {
            $this->assertTrue($this->assertTrue(true));
        }
        
        function testAssertFalseReturnsAssertionAsBoolean() {
            $this->assertTrue($this->assertFalse(false));
        }
        
        function testAssertEqualReturnsAssertionAsBoolean() {
            $this->assertTrue($this->assertEqual(5, 5));
        }
        
        function testAssertIdenticalReturnsAssertionAsBoolean() {
            $this->assertTrue($this->assertIdentical(5, 5));
        }
        
        function testCoreAssertionsDoNotThrowErrors() {
            $this->assertIsA($this, 'UnitTestCase');
            $this->assertNotA($this, 'WebTestCase');
        }
    }
    
    class JBehaveStyleRunner extends SimpleRunner {
        function __construct(&$test_case, &$scorer) {
            parent::__construct($test_case, $scorer);
        }
        
        function _isTest($method) {
            return strtolower(substr($method, 0, 6)) == 'should';
        }
    }
    
    class TestOfJBehaveStyleRunner extends UnitTestCase {
        
        function &_createRunner(&$reporter) {
            return new JBehaveStyleRunner($this, $reporter);
        }
        
        function testFail() {
            $this->fail('This should not be run');
        }
        
        function shouldBeRun() {
            $this->pass('This should be run');
        }
    }
