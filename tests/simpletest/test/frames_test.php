<?php
    // $Id: frames_test.php,v 1.1 2005/11/09 23:41:18 gsmet Exp $
    
    require_once(dirname(__FILE__) . '/../tag.php');
    require_once(dirname(__FILE__) . '/../page.php');
    require_once(dirname(__FILE__) . '/../frames.php');
    
    Mock::generate('SimplePage');
    Mock::generate('SimpleForm');
    
    class TestOfFrameset extends UnitTestCase {
        
        function testTitleReadFromFramesetPage() {
            $page = &new MockSimplePage($this);
            $page->setReturnValue('getTitle', 'This page');
            $frameset = &new SimpleFrameset($page);
            $this->assertEqual($frameset->getTitle(), 'This page');
        }
        
        function TestHeadersReadFromFramesetByDefault() {
            $page = &new MockSimplePage($this);
            $page->setReturnValue('getHeaders', 'Header: content');
            $page->setReturnValue('getMimeType', 'text/xml');
            $page->setReturnValue('getResponseCode', 401);
            $page->setReturnValue('getTransportError', 'Could not parse headers');
            $page->setReturnValue('getAuthentication', 'Basic');
            $page->setReturnValue('getRealm', 'Safe place');
            
            $frameset = &new SimpleFrameset($page);
            
            $this->assertIdentical($frameset->getHeaders(), 'Header: content');
            $this->assertIdentical($frameset->getMimeType(), 'text/xml');
            $this->assertIdentical($frameset->getResponseCode(), 401);
            $this->assertIdentical($frameset->getTransportError(), 'Could not parse headers');
            $this->assertIdentical($frameset->getAuthentication(), 'Basic');
            $this->assertIdentical($frameset->getRealm(), 'Safe place');
        }
        
        function testEmptyFramesetHasNoContent() {
            $page = &new MockSimplePage($this);
            $page->setReturnValue('getRaw', 'This content');
            $frameset = &new SimpleFrameset($page);
            $this->assertEqual($frameset->getRaw(), '');
        }
        
        function testRawContentIsFromOnlyFrame() {
            $page = &new MockSimplePage($this);
            $page->expectNever('getRaw');
            
            $frame = &new MockSimplePage($this);
            $frame->setReturnValue('getRaw', 'Stuff');
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame);
            $this->assertEqual($frameset->getRaw(), 'Stuff');
        }
        
        function testRawContentIsFromAllFrames() {
            $page = &new MockSimplePage($this);
            $page->expectNever('getRaw');
            
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getRaw', 'Stuff1');
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue('getRaw', 'Stuff2');
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame1);
            $frameset->addFrame($frame2);
            $this->assertEqual($frameset->getRaw(), 'Stuff1Stuff2');
        }
        
        function testTextContentIsFromOnlyFrame() {
            $page = &new MockSimplePage($this);
            $page->expectNever('getText');
            
            $frame = &new MockSimplePage($this);
            $frame->setReturnValue('getText', 'Stuff');
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame);
            $this->assertEqual($frameset->getText(), 'Stuff');
        }
        
        function testTextContentIsFromAllFrames() {
            $page = &new MockSimplePage($this);
            $page->expectNever('getText');
            
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getText', 'Stuff1');
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue('getText', 'Stuff2');
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame1);
            $frameset->addFrame($frame2);
            $this->assertEqual($frameset->getText(), 'Stuff1 Stuff2');
        }
        
        function testFieldIsFirstInFramelist() {
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getField', null);
            $frame1->expectOnce('getField', ['a']);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue('getField', 'A');
            $frame2->expectOnce('getField', ['a']);
            
            $frame3 = &new MockSimplePage($this);
            $frame3->expectNever('getField');
            
            $page = &new MockSimplePage($this);
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame1);
            $frameset->addFrame($frame2);
            $frameset->addFrame($frame3);
            
            $this->assertIdentical($frameset->getField('a'), 'A');
            $frame1->tally();
            $frame2->tally();
        }
        
        function testFrameReplacementByIndex() {
            $page = &new MockSimplePage($this);
            $page->expectNever('getRaw');
            
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getRaw', 'Stuff1');
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue('getRaw', 'Stuff2');
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame1);
            $frameset->setFrame([1], $frame2);
            $this->assertEqual($frameset->getRaw(), 'Stuff2');
        }
        
        function testFrameReplacementByName() {
            $page = &new MockSimplePage($this);
            $page->expectNever('getRaw');
            
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getRaw', 'Stuff1');
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue('getRaw', 'Stuff2');
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame1, 'a');
            $frameset->setFrame(['a'], $frame2);
            $this->assertEqual($frameset->getRaw(), 'Stuff2');
        }
    }
    
    class TestOfFrameNavigation extends UnitTestCase {
        
        function testStartsWithoutFrameFocus() {
            $page = &new MockSimplePage($this);
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame);
            $this->assertFalse($frameset->getFrameFocus());
        }
        
        function testCanFocusOnSingleFrame() {
            $page = &new MockSimplePage($this);
            $page->expectNever('getRaw');
            
            $frame = &new MockSimplePage($this);
            $frame->setReturnValue('getFrameFocus', []);
            $frame->setReturnValue('getRaw', 'Stuff');
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame);
            
            $this->assertFalse($frameset->setFrameFocusByIndex(0));
            $this->assertTrue($frameset->setFrameFocusByIndex(1));
            $this->assertEqual($frameset->getRaw(), 'Stuff');
            $this->assertFalse($frameset->setFrameFocusByIndex(2));
            $this->assertIdentical($frameset->getFrameFocus(), [1]);
        }
        
        function testContentComesFromFrameInFocus() {
            $page = &new MockSimplePage($this);
            
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getRaw', 'Stuff1');
            $frame1->setReturnValue('getFrameFocus', []);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue('getRaw', 'Stuff2');
            $frame2->setReturnValue('getFrameFocus', []);
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame1);
            $frameset->addFrame($frame2);
            
            $this->assertTrue($frameset->setFrameFocusByIndex(1));
            $this->assertEqual($frameset->getFrameFocus(), [1]);
            $this->assertEqual($frameset->getRaw(), 'Stuff1');
            
            $this->assertTrue($frameset->setFrameFocusByIndex(2));
            $this->assertEqual($frameset->getFrameFocus(), [2]);
            $this->assertEqual($frameset->getRaw(), 'Stuff2');
            
            $this->assertFalse($frameset->setFrameFocusByIndex(3));
            $this->assertEqual($frameset->getFrameFocus(), [2]);
            
            $frameset->clearFrameFocus();
            $this->assertEqual($frameset->getRaw(), 'Stuff1Stuff2');
        }
        function testCanFocusByName() {
            $page = &new MockSimplePage($this);
            
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getRaw', 'Stuff1');
            $frame1->setReturnValue('getFrameFocus', []);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue('getRaw', 'Stuff2');
            $frame2->setReturnValue('getFrameFocus', []);
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame1, 'A');
            $frameset->addFrame($frame2, 'B');
            
            $this->assertTrue($frameset->setFrameFocus('A'));
            $this->assertEqual($frameset->getFrameFocus(), ['A']);
            $this->assertEqual($frameset->getRaw(), 'Stuff1');
            
            $this->assertTrue($frameset->setFrameFocusByIndex(2));
            $this->assertEqual($frameset->getFrameFocus(), ['B']);
            $this->assertEqual($frameset->getRaw(), 'Stuff2');
            
            $this->assertFalse($frameset->setFrameFocus('z'));
            
            $frameset->clearFrameFocus();
            $this->assertEqual($frameset->getRaw(), 'Stuff1Stuff2');
        }
    }
    
    class TestOfFramesetPageInterface extends UnitTestCase {
        public $_page_interface;
        public $_frameset_interface;
        
        function __construct() {
            $this->UnitTestCase();
            $this->_page_interface = $this->_getPageMethods();
            $this->_frameset_interface = $this->_getFramesetMethods();
        }
        
        function assertListInAnyOrder($list, $expected) {
            sort($list);
            sort($expected);
            $this->assertEqual($list, $expected);
        }
        
        function _getPageMethods() {
            $methods = [];
            foreach (get_class_methods('SimplePage') as $method) {
                if (strtolower($method) == strtolower('SimplePage')) {
                    continue;
                }
                if (strtolower($method) == strtolower('getFrameset')) {
                    continue;
                }
                if (strncmp($method, '_', 1) == 0) {
                    continue;
                }
                if (strncmp($method, 'accept', 6) == 0) {
                    continue;
                }
                $methods[] = $method;
            }
            return $methods;
        }
        
        function _getFramesetMethods() {
            $methods = [];
            foreach (get_class_methods('SimpleFrameset') as $method) {
                if (strtolower($method) == strtolower('SimpleFrameset')) {
                    continue;
                }
                if (strncmp($method, '_', 1) == 0) {
                    continue;
                }
                if (strncmp($method, 'add', 3) == 0) {
                    continue;
                }
                $methods[] = $method;
            }
            return $methods;
        }
        
        function testFramsetHasPageInterface() {
            $difference = [];
            foreach ($this->_page_interface as $method) {
                if (! in_array($method, $this->_frameset_interface)) {
                    $this->fail("No [$method] in Frameset class");
                    return;
                }
            }
            $this->pass('Frameset covers Page interface');
        }
        
        function testHeadersReadFromFrameIfInFocus() {
            $frame = &new MockSimplePage($this);
            $frame->setReturnValue('getUrl', new SimpleUrl('http://localhost/stuff'));
            
            $frame->setReturnValue('getRequest', 'POST stuff');
            $frame->setReturnValue('getMethod', 'POST');
            $frame->setReturnValue('getRequestData', ['a' => 'A']);
            $frame->setReturnValue('getHeaders', 'Header: content');
            $frame->setReturnValue('getMimeType', 'text/xml');
            $frame->setReturnValue('getResponseCode', 401);
            $frame->setReturnValue('getTransportError', 'Could not parse headers');
            $frame->setReturnValue('getAuthentication', 'Basic');
            $frame->setReturnValue('getRealm', 'Safe place');
            
            $frameset = &new SimpleFrameset(new MockSimplePage($this));
            $frameset->addFrame($frame);
            $frameset->setFrameFocusByIndex(1);
            
            $url = new SimpleUrl('http://localhost/stuff');
            $url->setTarget(1);
            $this->assertIdentical($frameset->getUrl(), $url);
            
            $this->assertIdentical($frameset->getRequest(), 'POST stuff');
            $this->assertIdentical($frameset->getMethod(), 'POST');
            $this->assertIdentical($frameset->getRequestData(), ['a' => 'A']);
            $this->assertIdentical($frameset->getHeaders(), 'Header: content');
            $this->assertIdentical($frameset->getMimeType(), 'text/xml');
            $this->assertIdentical($frameset->getResponseCode(), 401);
            $this->assertIdentical($frameset->getTransportError(), 'Could not parse headers');
            $this->assertIdentical($frameset->getAuthentication(), 'Basic');
            $this->assertIdentical($frameset->getRealm(), 'Safe place');
        }
        
        function testAbsoluteUrlsComeFromBothFrames() {
            $page = &new MockSimplePage($this);
            $page->expectNever('getAbsoluteUrls');
            
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue(
                    'getAbsoluteUrls',
                    ['http://www.lastcraft.com/', 'http://myserver/']);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue(
                    'getAbsoluteUrls',
                    ['http://www.lastcraft.com/', 'http://test/']);
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame1);
            $frameset->addFrame($frame2);
            $this->assertListInAnyOrder(
                    $frameset->getAbsoluteUrls(),
                    ['http://www.lastcraft.com/', 'http://myserver/', 'http://test/']);
        }
        
        function testRelativeUrlsComeFromBothFrames() {
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue(
                    'getRelativeUrls',
                    ['/', '.', '/test/', 'goodbye.php']);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue(
                    'getRelativeUrls',
                    ['/', '..', '/test/', 'hello.php']);
            
            $page = &new MockSimplePage($this);
            $page->expectNever('getRelativeUrls');
            
            $frameset = &new SimpleFrameset($page);
            $frameset->addFrame($frame1);
            $frameset->addFrame($frame2);
            $this->assertListInAnyOrder(
                    $frameset->getRelativeUrls(),
                    ['/', '.', '/test/', 'goodbye.php', '..', 'hello.php']);
        }
        
        function testLabelledUrlsComeFromBothFrames() {
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue(
                    'getUrlsByLabel',
                    [new SimpleUrl('goodbye.php')],
                    ['a']);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue(
                    'getUrlsByLabel',
                    [new SimpleUrl('hello.php')],
                    ['a']);
            
            $frameset = &new SimpleFrameset(new MockSimplePage($this));
            $frameset->addFrame($frame1);
            $frameset->addFrame($frame2, 'Two');
            
            $expected1 = new SimpleUrl('goodbye.php');
            $expected1->setTarget(1);
            $expected2 = new SimpleUrl('hello.php');
            $expected2->setTarget('Two');
            $this->assertEqual(
                    $frameset->getUrlsByLabel('a'),
                    [$expected1, $expected2]);
        }
        
        function testUrlByIdComesFromFirstFrameToRespond() {
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getUrlById', new SimpleUrl('four.php'), [4]);
            $frame1->setReturnValue('getUrlById', false, [5]);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->setReturnValue('getUrlById', false, [4]);
            $frame2->setReturnValue('getUrlById', new SimpleUrl('five.php'), [5]);
            
            $frameset = &new SimpleFrameset(new MockSimplePage($this));
            $frameset->addFrame($frame1);
            $frameset->addFrame($frame2);
            
            $four = new SimpleUrl('four.php');
            $four->setTarget(1);
            $this->assertEqual($frameset->getUrlById(4), $four);
            $five = new SimpleUrl('five.php');
            $five->setTarget(2);
            $this->assertEqual($frameset->getUrlById(5), $five);
        }
        
        function testReadUrlsFromFrameInFocus() {
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getAbsoluteUrls', ['a']);
            $frame1->setReturnValue('getRelativeUrls', ['r']);
            $frame1->setReturnValue('getUrlsByLabel', [new SimpleUrl('l')]);
            $frame1->setReturnValue('getUrlById', new SimpleUrl('i'));
            
            $frame2 = &new MockSimplePage($this);
            $frame2->expectNever('getAbsoluteUrls');
            $frame2->expectNever('getRelativeUrls');
            $frame2->expectNever('getUrlsByLabel');
            $frame2->expectNever('getUrlById');

            $frameset = &new SimpleFrameset(new MockSimplePage($this));
            $frameset->addFrame($frame1, 'A');
            $frameset->addFrame($frame2, 'B');
            $frameset->setFrameFocus('A');
            
            $this->assertIdentical($frameset->getAbsoluteUrls(), ['a']);
            $this->assertIdentical($frameset->getRelativeUrls(), ['r']);
            $expected = new SimpleUrl('l');
            $expected->setTarget('A');
            $this->assertIdentical($frameset->getUrlsByLabel('label'), [$expected]);
            $expected = new SimpleUrl('i');
            $expected->setTarget('A');
            $this->assertIdentical($frameset->getUrlById(99), $expected);
        }
          
        function testReadFrameTaggedUrlsFromFrameInFocus() {
            $frame = &new MockSimplePage($this);
            
            $by_label = new SimpleUrl('l');
            $by_label->setTarget('L');
            $frame->setReturnValue('getUrlsByLabel', [$by_label]);
            
            $by_id = new SimpleUrl('i');
            $by_id->setTarget('I');
            $frame->setReturnValue('getUrlById', $by_id);
            
            $frameset = &new SimpleFrameset(new MockSimplePage($this));
            $frameset->addFrame($frame, 'A');
            $frameset->setFrameFocus('A');
            
            $this->assertIdentical($frameset->getUrlsByLabel('label'), [$by_label]);
            $this->assertIdentical($frameset->getUrlById(99), $by_id);
        }
      
        function testFindingFormsByAllFinders() {
            $finders = [
                    'getFormBySubmitLabel', 'getFormBySubmitName',
                    'getFormBySubmitId', 'getFormByImageLabel',
                    'getFormByImageName', 'getFormByImageId', 'getFormById'];
            $forms = [];
            
            $frame = &new MockSimplePage($this);
            for ($i = 0; $i < count($finders); $i++) {
                $forms[$i] = &new MockSimpleForm($this);
                $frame->setReturnReference($finders[$i], $forms[$i], ['a']);
            }

            $frameset = &new SimpleFrameset(new MockSimplePage($this));
            $frameset->addFrame(new MockSimplePage($this), 'A');
            $frameset->addFrame($frame, 'B');
            for ($i = 0; $i < count($finders); $i++) {
                $method = $finders[$i];
                $this->assertReference($frameset->$method('a'), $forms[$i]);
            }
           
            $frameset->setFrameFocus('A');
            for ($i = 0; $i < count($finders); $i++) {
                $method = $finders[$i];
                $this->assertNull($frameset->$method('a'));
            }
           
            $frameset->setFrameFocus('B');
            for ($i = 0; $i < count($finders); $i++) {
                $method = $finders[$i];
                $this->assertReference($frameset->$method('a'), $forms[$i]);
            }
        }
        
        function testSettingAllFrameFieldsWhenNoFrameFocus() {
            $frame1 = &new MockSimplePage($this);
            $frame1->expectOnce('setField', ['a', 'A']);
            $frame1->expectOnce('setFieldById', [22, 'A']);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->expectOnce('setField', ['a', 'A']);
            $frame2->expectOnce('setFieldById', [22, 'A']);
            
            $frameset = &new SimpleFrameset(new MockSimplePage($this));
            $frameset->addFrame($frame1, 'A');
            $frameset->addFrame($frame2, 'B');
            
            $frameset->setField('a', 'A');
            $frameset->setFieldById(22, 'A');
            $frame1->tally();
            $frame2->tally();
        }
        
        function testOnlySettingFieldFromFocusedFrame() {
            $frame1 = &new MockSimplePage($this);
            $frame1->expectOnce('setField', ['a', 'A']);
            $frame1->expectOnce('setFieldById', [22, 'A']);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->expectNever('setField');
            $frame2->expectNever('setFieldById');
            
            $frameset = &new SimpleFrameset(new MockSimplePage($this));
            $frameset->addFrame($frame1, 'A');
            $frameset->addFrame($frame2, 'B');
            $frameset->setFrameFocus('A');
            
            $frameset->setField('a', 'A');
            $frameset->setFieldById(22, 'A');
            $frame1->tally();
        }
        
        function testOnlyGettingFieldFromFocusedFrame() {
            $frame1 = &new MockSimplePage($this);
            $frame1->setReturnValue('getField', 'f', ['a']);
            $frame1->setReturnValue('getFieldById', 'i', [7]);
            
            $frame2 = &new MockSimplePage($this);
            $frame2->expectNever('getField');
            $frame2->expectNever('getFieldById');
            
            $frameset = &new SimpleFrameset(new MockSimplePage($this));
            $frameset->addFrame($frame1, 'A');
            $frameset->addFrame($frame2, 'B');
            $frameset->setFrameFocus('A');
            
            $this->assertIdentical($frameset->getField('a'), 'f');
            $this->assertIdentical($frameset->getFieldById(7), 'i');
        }
    }
