<?php
    // $Id: tag_test.php,v 1.1 2005/11/09 23:41:18 gsmet Exp $
    
    require_once(dirname(__FILE__) . '/../tag.php');
    
    class TestOfTag extends UnitTestCase {
        
        function testStartValuesWithoutAdditionalContent() {
            $tag = new SimpleTitleTag(['a' => '1', 'b' => '']);
            $this->assertEqual($tag->getTagName(), 'title');
            $this->assertIdentical($tag->getAttribute('a'), '1');
            $this->assertIdentical($tag->getAttribute('b'), true);
            $this->assertIdentical($tag->getAttribute('c'), false);
            $this->assertIdentical($tag->getContent(), '');
        }
        
        function testTitleContent() {
            $tag = new SimpleTitleTag([]);
            $this->assertTrue($tag->expectEndTag());
            $tag->addContent('Hello');
            $tag->addContent('World');
            $this->assertEqual($tag->getText(), 'HelloWorld');
        }
        
        function testMessyTitleContent() {
            $tag = new SimpleTitleTag([]);
            $this->assertTrue($tag->expectEndTag());
            $tag->addContent('<b>Hello</b>');
            $tag->addContent('<em>World</em>');
            $this->assertEqual($tag->getText(), 'HelloWorld');
        }
        
        function testTagWithNoEnd() {
            $tag = new SimpleTextTag([]);
            $this->assertFalse($tag->expectEndTag());
        }
        
        function testAnchorHref() {
            $tag = new SimpleAnchorTag(['href' => 'http://here/']);
            $this->assertEqual($tag->getHref(), 'http://here/');
            
            $tag = new SimpleAnchorTag(['href' => '']);
            $this->assertIdentical($tag->getAttribute('href'), true);
            $this->assertIdentical($tag->getHref(), '');
            
            $tag = new SimpleAnchorTag([]);
            $this->assertIdentical($tag->getAttribute('href'), false);
            $this->assertIdentical($tag->getHref(), '');
        }
        
        function testIsIdMatchesIdAttribute() {
            $tag = new SimpleAnchorTag(['href' => 'http://here/', 'id' => 7]);
            $this->assertIdentical($tag->getAttribute('id'), '7');
            $this->assertTrue($tag->isId(7));
        }
    }
    
    class TestOfWidget extends UnitTestCase {
        
        function testTextEmptyDefault() {
            $tag = new SimpleTextTag(['' => 'text']);
            $this->assertIdentical($tag->getDefault(), '');
            $this->assertIdentical($tag->getValue(), '');
        }
        
        function testTextDefault() {
            $tag = new SimpleTextTag(['value' => 'aaa']);
            $this->assertEqual($tag->getDefault(), 'aaa');
            $this->assertEqual($tag->getValue(), 'aaa');
        }
        
        function testSettingTextValue() {
            $tag = new SimpleTextTag(['value' => 'aaa']);
            $tag->setValue('bbb');
            $this->assertEqual($tag->getValue(), 'bbb');
            $tag->resetValue();
            $this->assertEqual($tag->getValue(), 'aaa');
        }
        
        function testFailToSetHiddenValue() {
            $tag = new SimpleTextTag(['value' => 'aaa', 'type' => 'hidden']);
            $this->assertFalse($tag->setValue('bbb'));
            $this->assertEqual($tag->getValue(), 'aaa');
        }
        
        function testSubmitDefaults() {
            $tag = new SimpleSubmitTag(['type' => 'submit']);
            $this->assertEqual($tag->getName(), 'submit');
            $this->assertEqual($tag->getValue(), 'Submit');
            $this->assertFalse($tag->setValue('Cannot set this'));
            $this->assertEqual($tag->getValue(), 'Submit');
            $this->assertEqual($tag->getLabel(), 'Submit');
            $this->assertEqual($tag->getSubmitValues(), ['submit' => 'Submit']);
        }
        
        function testPopulatedSubmit() {
            $tag = new SimpleSubmitTag(
                    ['type' => 'submit', 'name' => 's', 'value' => 'Ok!']);
            $this->assertEqual($tag->getName(), 's');
            $this->assertEqual($tag->getValue(), 'Ok!');
            $this->assertEqual($tag->getLabel(), 'Ok!');
            $this->assertEqual($tag->getSubmitValues(), ['s' => 'Ok!']);
        }
        
        function testImageSubmit() {
            $tag = new SimpleImageSubmitTag(
                    ['type' => 'image', 'name' => 's', 'alt' => 'Label']);
            $this->assertEqual($tag->getName(), 's');
            $this->assertEqual($tag->getLabel(), 'Label');
            $this->assertEqual(
                    $tag->getSubmitValues(20, 30),
                    ['s.x' => 20, 's.y' => 30]);
        }
        
        function testImageSubmitTitlePreferredOverAltForLabel() {
            $tag = new SimpleImageSubmitTag(
                    ['type' => 'image', 'name' => 's', 'alt' => 'Label', 'title' => 'Title']);
            $this->assertEqual($tag->getLabel(), 'Title');
        }
        
        function testButton() {
            $tag = new SimpleButtonTag(
                    ['type' => 'submit', 'name' => 's', 'value' => 'do']);
            $tag->addContent('I am a button');
            $this->assertEqual($tag->getName(), 's');
            $this->assertEqual($tag->getValue(), 'do');
            $this->assertEqual($tag->getLabel(), 'I am a button');
            $this->assertEqual($tag->getSubmitValues(), ['s' => 'do']);
        }
    }
    
    class TestOfTextArea extends UnitTestCase {
        
        function testDefault() {
            $tag = new SimpleTextAreaTag(['name' => 'a']);
            $tag->addContent('Some text');
            $this->assertEqual($tag->getName(), 'a');
            $this->assertEqual($tag->getDefault(), 'Some text');
        }
        
        function testWrapping() {
            $tag = new SimpleTextAreaTag(['cols' => '10', 'wrap' => 'physical']);
            $tag->addContent("Lot's of text that should be wrapped");
            $this->assertEqual(
                    $tag->getDefault(),
                    "Lot's of\ntext that\nshould be\nwrapped");
            $tag->setValue("New long text\nwith two lines");
            $this->assertEqual(
                    $tag->getValue(),
                    "New long\ntext\nwith two\nlines");
        }
    }
    
    class TestOfSelection extends UnitTestCase {
        
        function testEmpty() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $this->assertIdentical($tag->getValue(), '');
        }
        
        function testSingle() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $option = new SimpleOptionTag([]);
            $option->addContent('AAA');
            $tag->addTag($option);
            $this->assertEqual($tag->getValue(), 'AAA');
        }
        
        function testSingleDefault() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $option = new SimpleOptionTag(['selected' => '']);
            $option->addContent('AAA');
            $tag->addTag($option);
            $this->assertEqual($tag->getValue(), 'AAA');
        }
        
        function testSingleMappedDefault() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $option = new SimpleOptionTag(['selected' => '', 'value' => 'aaa']);
            $option->addContent('AAA');
            $tag->addTag($option);
            $this->assertEqual($tag->getValue(), 'aaa');
        }
        
        function testStartsWithDefault() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $a = new SimpleOptionTag([]);
            $a->addContent('AAA');
            $tag->addTag($a);
            $b = new SimpleOptionTag(['selected' => '']);
            $b->addContent('BBB');
            $tag->addTag($b);
            $c = new SimpleOptionTag([]);
            $c->addContent('CCC');
            $tag->addTag($c);
            $this->assertEqual($tag->getValue(), 'BBB');
        }
        
        function testSettingOption() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $a = new SimpleOptionTag([]);
            $a->addContent('AAA');
            $tag->addTag($a);
            $b = new SimpleOptionTag(['selected' => '']);
            $b->addContent('BBB');
            $tag->addTag($b);
            $c = new SimpleOptionTag([]);
            $c->addContent('CCC');
            $tag->setValue('AAA');
            $this->assertEqual($tag->getValue(), 'AAA');
        }
        
        function testSettingMappedOption() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $a = new SimpleOptionTag(['value' => 'aaa']);
            $a->addContent('AAA');
            $tag->addTag($a);
            $b = new SimpleOptionTag(['value' => 'bbb', 'selected' => '']);
            $b->addContent('BBB');
            $tag->addTag($b);
            $c = new SimpleOptionTag(['value' => 'ccc']);
            $c->addContent('CCC');
            $tag->addTag($c);
            $tag->setValue('AAA');
            $this->assertEqual($tag->getValue(), 'aaa');
        }
        
        function testSelectionDespiteSpuriousWhitespace() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $a = new SimpleOptionTag([]);
            $a->addContent(' AAA ');
            $tag->addTag($a);
            $b = new SimpleOptionTag(['selected' => '']);
            $b->addContent(' BBB ');
            $tag->addTag($b);
            $c = new SimpleOptionTag([]);
            $c->addContent(' CCC ');
            $tag->addTag($c);
            $this->assertEqual($tag->getValue(), ' BBB ');
            $tag->setValue('AAA');
            $this->assertEqual($tag->getValue(), ' AAA ');
        }
        
        function testFailToSetIllegalOption() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $a = new SimpleOptionTag([]);
            $a->addContent('AAA');
            $tag->addTag($a);
            $b = new SimpleOptionTag(['selected' => '']);
            $b->addContent('BBB');
            $tag->addTag($b);
            $c = new SimpleOptionTag([]);
            $c->addContent('CCC');
            $tag->addTag($c);
            $this->assertFalse($tag->setValue('Not present'));
            $this->assertEqual($tag->getValue(), 'BBB');
        }
        
        function testNastyOptionValuesThatLookLikeFalse() {
            $tag = new SimpleSelectionTag(['name' => 'a']);
            $a = new SimpleOptionTag(['value' => '1']);
            $a->addContent('One');
            $tag->addTag($a);
            $b = new SimpleOptionTag(['value' => '0']);
            $b->addContent('Zero');
            $tag->addTag($b);
            $this->assertIdentical($tag->getValue(), '1');
            $tag->setValue('Zero');
            $this->assertIdentical($tag->getValue(), '0');
        }
        
        function testBlankOption() {
            $tag = new SimpleSelectionTag(['name' => 'A']);
            $a = new SimpleOptionTag([]);
            $tag->addTag($a);
            $b = new SimpleOptionTag([]);
            $b->addContent('b');
            $tag->addTag($b);
            $this->assertIdentical($tag->getValue(), '');
            $tag->setValue('b');
            $this->assertIdentical($tag->getValue(), 'b');
            $tag->setValue('');
            $this->assertIdentical($tag->getValue(), '');
        }
        
        function testMultipleDefaultWithNoSelections() {
            $tag = new MultipleSelectionTag(['name' => 'a', 'multiple' => '']);
            $a = new SimpleOptionTag([]);
            $a->addContent('AAA');
            $tag->addTag($a);
            $b = new SimpleOptionTag([]);
            $b->addContent('BBB');
            $tag->addTag($b);
            $this->assertIdentical($tag->getDefault(), []);
            $this->assertIdentical($tag->getValue(), []);
        }
        
        function testMultipleDefaultWithSelections() {
            $tag = new MultipleSelectionTag(['name' => 'a', 'multiple' => '']);
            $a = new SimpleOptionTag(['selected' => '']);
            $a->addContent('AAA');
            $tag->addTag($a);
            $b = new SimpleOptionTag(['selected' => '']);
            $b->addContent('BBB');
            $tag->addTag($b);
            $this->assertIdentical($tag->getDefault(), ['AAA', 'BBB']);
            $this->assertIdentical($tag->getValue(), ['AAA', 'BBB']);
        }
        
        function testSettingMultiple() {
            $tag = new MultipleSelectionTag(['name' => 'a', 'multiple' => '']);
            $a = new SimpleOptionTag(['selected' => '']);
            $a->addContent('AAA');
            $tag->addTag($a);
            $b = new SimpleOptionTag([]);
            $b->addContent('BBB');
            $tag->addTag($b);
            $c = new SimpleOptionTag(['selected' => '']);
            $c->addContent('CCC');
            $tag->addTag($c);
            $this->assertIdentical($tag->getDefault(), ['AAA', 'CCC']);
            $this->assertTrue($tag->setValue(['BBB', 'CCC']));
            $this->assertIdentical($tag->getValue(), ['BBB', 'CCC']);
            $this->assertTrue($tag->setValue([]));
            $this->assertIdentical($tag->getValue(), []);
        }
        
        function testFailToSetIllegalOptionsInMultiple() {
            $tag = new MultipleSelectionTag(['name' => 'a', 'multiple' => '']);
            $a = new SimpleOptionTag(['selected' => '']);
            $a->addContent('AAA');
            $tag->addTag($a);
            $b = new SimpleOptionTag([]);
            $b->addContent('BBB');
            $tag->addTag($b);
            $this->assertFalse($tag->setValue(['CCC']));
            $this->assertTrue($tag->setValue(['AAA', 'BBB']));
            $this->assertFalse($tag->setValue(['AAA', 'CCC']));
        }
    }
    
    class TestOfRadioGroup extends UnitTestCase {
        
        function testEmptyGroup() {
            $group = new SimpleRadioGroup();
            $this->assertIdentical($group->getDefault(), false);
            $this->assertIdentical($group->getValue(), false);
            $this->assertFalse($group->setValue('a'));
        }
        
        function testReadingSingleButtonGroup() {
            $group = new SimpleRadioGroup();
            $group->addWidget(new SimpleRadioButtonTag(
                    ['value' => 'A', 'checked' => '']));
            $this->assertIdentical($group->getDefault(), 'A');
            $this->assertIdentical($group->getValue(), 'A');
        }
        
        function testReadingMultipleButtonGroup() {
            $group = new SimpleRadioGroup();
            $group->addWidget(new SimpleRadioButtonTag(
                    ['value' => 'A']));
            $group->addWidget(new SimpleRadioButtonTag(
                    ['value' => 'B', 'checked' => '']));
            $this->assertIdentical($group->getDefault(), 'B');
            $this->assertIdentical($group->getValue(), 'B');
        }
        
        function testFailToSetUnlistedValue() {
            $group = new SimpleRadioGroup();
            $group->addWidget(new SimpleRadioButtonTag(['value' => 'z']));
            $this->assertFalse($group->setValue('a'));
            $this->assertIdentical($group->getValue(), false);
        }
        
        function testSettingNewValueClearsTheOldOne() {
            $group = new SimpleRadioGroup();
            $group->addWidget(new SimpleRadioButtonTag(
                    ['value' => 'A']));
            $group->addWidget(new SimpleRadioButtonTag(
                    ['value' => 'B', 'checked' => '']));
            $this->assertTrue($group->setValue('A'));
            $this->assertIdentical($group->getValue(), 'A');
        }
        
        function testIsIdMatchesAnyWidgetInSet() {
            $group = new SimpleRadioGroup();
            $group->addWidget(new SimpleRadioButtonTag(
                    ['value' => 'A', 'id' => 'i1']));
            $group->addWidget(new SimpleRadioButtonTag(
                    ['value' => 'B', 'id' => 'i2']));
            $this->assertFalse($group->isId('i0'));
            $this->assertTrue($group->isId('i1'));
            $this->assertTrue($group->isId('i2'));
        }
    }
    
    class TestOfTagGroup extends UnitTestCase {
        
        function testReadingMultipleCheckboxGroup() {
            $group = new SimpleCheckboxGroup();
            $group->addWidget(new SimpleCheckboxTag(['value' => 'A']));
            $group->addWidget(new SimpleCheckboxTag(
                    ['value' => 'B', 'checked' => '']));
            $this->assertIdentical($group->getDefault(), 'B');
            $this->assertIdentical($group->getValue(), 'B');
        }
        
        function testReadingMultipleUncheckedItems() {
            $group = new SimpleCheckboxGroup();
            $group->addWidget(new SimpleCheckboxTag(['value' => 'A']));
            $group->addWidget(new SimpleCheckboxTag(['value' => 'B']));            
            $this->assertIdentical($group->getDefault(), false);
            $this->assertIdentical($group->getValue(), false);
        }
        
        function testReadingMultipleCheckedItems() {
            $group = new SimpleCheckboxGroup();
            $group->addWidget(new SimpleCheckboxTag(
                    ['value' => 'A', 'checked' => '']));
            $group->addWidget(new SimpleCheckboxTag(
                    ['value' => 'B', 'checked' => '']));
            $this->assertIdentical($group->getDefault(), ['A', 'B']);
            $this->assertIdentical($group->getValue(), ['A', 'B']);
        }
        
        function testSettingSingleValue() {
            $group = new SimpleCheckboxGroup();
            $group->addWidget(new SimpleCheckboxTag(['value' => 'A']));
            $group->addWidget(new SimpleCheckboxTag(['value' => 'B']));
            $this->assertTrue($group->setValue('A'));
            $this->assertIdentical($group->getValue(), 'A');
            $this->assertTrue($group->setValue('B'));
            $this->assertIdentical($group->getValue(), 'B');
        }
        
        function testSettingMultipleValues() {
            $group = new SimpleCheckboxGroup();
            $group->addWidget(new SimpleCheckboxTag(['value' => 'A']));
            $group->addWidget(new SimpleCheckboxTag(['value' => 'B']));
            $this->assertTrue($group->setValue(['A', 'B']));
            $this->assertIdentical($group->getValue(), ['A', 'B']);
        }
        
        function testSettingNoValue() {
            $group = new SimpleCheckboxGroup();
            $group->addWidget(new SimpleCheckboxTag(['value' => 'A']));
            $group->addWidget(new SimpleCheckboxTag(['value' => 'B']));
            $this->assertTrue($group->setValue(false));
            $this->assertIdentical($group->getValue(), false);
        }
        
        function testIsIdMatchesAnyIdInSet() {
            $group = new SimpleCheckboxGroup();
            $group->addWidget(new SimpleCheckboxTag(['id' => 1, 'value' => 'A']));
            $group->addWidget(new SimpleCheckboxTag(['id' => 2, 'value' => 'B']));
            $this->assertFalse($group->isId(0));
            $this->assertTrue($group->isId(1));
            $this->assertTrue($group->isId(2));
        }
    }
