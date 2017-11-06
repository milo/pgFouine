<?php
    // $Id: adapter_test.php,v 1.1 2005/11/09 23:41:18 gsmet Exp $
    
    class SameTestClass {
    }
    
    class TestOfPearAdapter extends PHPUnit_TestCase {
        
        function testBoolean() {
            $this->assertTrue(true, "PEAR true");
            $this->assertFalse(false, "PEAR false");
        }
        
        function testName() {
            $this->assertTrue($this->getName() == get_class($this));
        }
        
        function testPass() {
            $this->pass("PEAR pass");
        }
        
        function testNulls() {
            $value = null;
            $this->assertNull($value, "PEAR null");
            $value = 0;
            $this->assertNotNull($value, "PEAR not null");
        }
        
        function testType() {
            $this->assertType("Hello", "string", "PEAR type");
        }
        
        function testEquals() {
            $this->assertEquals(12, 12, "PEAR identity");
            $this->setLooselyTyped(true);
            $this->assertEquals("12", 12, "PEAR equality");
        }
        
        function testSame() {
            $same = new SameTestClass();
            $this->assertSame($same, $same, "PEAR same");
        }
        
        function testRegExp() {
            $this->assertRegExp('/hello/', "A big hello from me", "PEAR regex");
        }
    }
    
    class TestOfPhpUnitAdapter extends TestCase {
        function __construct() {
            parent::__construct("TestOfPhpUnitAdapter");
        }
        
        function testBoolean() {
            $this->assert(true, "PHP Unit true");
        }
        
        function testName() {
            $this->assertTrue($this->name() == "TestOfPhpUnitAdapter");
        }
        
        function testEquals() {
            $this->assertEquals(12, 12, "PHP Unit equality");
        }
        
        function testMultilineEquals() {
            $this->assertEquals("a\nb\n", "a\nb\n", "PHP Unit equality");
        }
        
        function testRegExp() {
            $this->assertRegexp('/hello/', "A big hello from me", "PEAR regex");
        }
    }
