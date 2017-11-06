<?php
    // $Id: xml_test.php,v 1.1 2005/11/09 23:41:18 gsmet Exp $
    
    require_once(dirname(__FILE__) . '/../xml.php');
    
    Mock::generate('SimpleScorer');
    
    if (! function_exists('xml_parser_create')) {
        SimpleTestOptions::ignore('TestOfXmlStructureParsing');
        SimpleTestOptions::ignore('TestOfXmlResultsParsing');
    }
    
    class TestOfNestingTags extends UnitTestCase {
        
        function testGroupSize() {
            $nesting = new NestingGroupTag(['SIZE' => 2]);
            $this->assertEqual($nesting->getSize(), 2);
        }
    }
    
    class TestOfXmlStructureParsing extends UnitTestCase {
        
        function testValidXml() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectNever('paintGroupStart');
            $listener->expectNever('paintGroupEnd');
            $listener->expectNever('paintCaseStart');
            $listener->expectNever('paintCaseEnd');
            $parser = &new SimpleTestXmlParser($listener);
            $this->assertTrue($parser->parse("<?xml version=\"1.0\"?>\n"));
            $this->assertTrue($parser->parse("<run>\n"));
            $this->assertTrue($parser->parse("</run>\n"));
        }
        
        function testEmptyGroup() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectOnce('paintGroupStart', ['a_group', 7]);
            $listener->expectOnce('paintGroupEnd', ['a_group']);
            $parser = &new SimpleTestXmlParser($listener);
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $this->assertTrue($parser->parse("<group size=\"7\">\n"));
            $this->assertTrue($parser->parse("<name>a_group</name>\n"));
            $this->assertTrue($parser->parse("</group>\n"));
            $parser->parse("</run>\n");
            $listener->tally();
        }
        
        function testEmptyCase() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectOnce('paintCaseStart', ['a_case']);
            $listener->expectOnce('paintCaseEnd', ['a_case']);
            $parser = &new SimpleTestXmlParser($listener);
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $this->assertTrue($parser->parse("<case>\n"));
            $this->assertTrue($parser->parse("<name>a_case</name>\n"));
            $this->assertTrue($parser->parse("</case>\n"));
            $parser->parse("</run>\n");
            $listener->tally();
        }
        
        function testEmptyMethod() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectOnce('paintCaseStart', ['a_case']);
            $listener->expectOnce('paintCaseEnd', ['a_case']);
            $listener->expectOnce('paintMethodStart', ['a_method']);
            $listener->expectOnce('paintMethodEnd', ['a_method']);
            $parser = &new SimpleTestXmlParser($listener);
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $parser->parse("<case>\n");
            $parser->parse("<name>a_case</name>\n");
            $this->assertTrue($parser->parse("<test>\n"));
            $this->assertTrue($parser->parse("<name>a_method</name>\n"));
            $this->assertTrue($parser->parse("</test>\n"));
            $parser->parse("</case>\n");
            $parser->parse("</run>\n");
            $listener->tally();
        }
        
        function testNestedGroup() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectArgumentsAt(0, 'paintGroupStart', ['a_group', 7]);
            $listener->expectArgumentsAt(1, 'paintGroupStart', ['b_group', 3]);
            $listener->expectCallCount('paintGroupStart', 2);
            $listener->expectArgumentsAt(0, 'paintGroupEnd', ['b_group']);
            $listener->expectArgumentsAt(1, 'paintGroupEnd', ['a_group']);
            $listener->expectCallCount('paintGroupEnd', 2);
            $parser = &new SimpleTestXmlParser($listener);
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $this->assertTrue($parser->parse("<group size=\"7\">\n"));
            $this->assertTrue($parser->parse("<name>a_group</name>\n"));
            $this->assertTrue($parser->parse("<group size=\"3\">\n"));
            $this->assertTrue($parser->parse("<name>b_group</name>\n"));
            $this->assertTrue($parser->parse("</group>\n"));
            $this->assertTrue($parser->parse("</group>\n"));
            $parser->parse("</run>\n");
            $listener->tally();
        }
    }
    
    class AnyOldSignal {
        public $stuff = true;
    }
    
    class TestOfXmlResultsParsing extends UnitTestCase {
        
        function sendValidStart(&$parser) {
            $parser->parse("<?xml version=\"1.0\"?>\n");
            $parser->parse("<run>\n");
            $parser->parse("<case>\n");
            $parser->parse("<name>a_case</name>\n");
            $parser->parse("<test>\n");
            $parser->parse("<name>a_method</name>\n");
        }
        
        function sendValidEnd(&$parser) {
            $parser->parse("</test>\n");
            $parser->parse("</case>\n");
            $parser->parse("</run>\n");
        }
        
        function testPass() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectOnce('paintPass', ['a_message']);
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<pass>a_message</pass>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        
        function testFail() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectOnce('paintFail', ['a_message']);
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<fail>a_message</fail>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        
        function testException() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectOnce('paintException', ['a_message']);
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<exception>a_message</exception>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        
        function testSignal() {
            $signal = new AnyOldSignal();
            $signal->stuff = "Hello";
            $listener = &new MockSimpleScorer($this);
            $listener->expectOnce('paintSignal', ['a_signal', $signal]);
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse(
                    "<signal type=\"a_signal\"><![CDATA[" .
                    serialize($signal) . "]]></signal>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        
        function testMessage() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectOnce('paintMessage', ['a_message']);
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<message>a_message</message>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
        
        function testFormattedMessage() {
            $listener = &new MockSimpleScorer($this);
            $listener->expectOnce('paintFormattedMessage', ["\na\tmessage\n"]);
            $parser = &new SimpleTestXmlParser($listener);
            $this->sendValidStart($parser);
            $this->assertTrue($parser->parse("<formatted><![CDATA[\na\tmessage\n]]></formatted>\n"));
            $this->sendValidEnd($parser);
            $listener->tally();
        }
    }
