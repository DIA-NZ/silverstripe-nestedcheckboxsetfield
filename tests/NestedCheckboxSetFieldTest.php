<?php

class NestedCheckboxSetFieldTest extends SapphireTest {

    public function testSource() {
        $field = $this->createField();

        $source = $field->getSource();

        $this->assertEquals(2, count($source));
        $this->assertEquals(2, count($source['Test Top One']));
        $this->assertEquals(1, count($source['Test Top Two']));
    }

    private function createField() {
        return new NestedCheckboxSetField('TestField', 'Test field', array(
            'Test Top One' => array(
                '1' => 'Test Item One',
                '2' => 'Test Item Two'
            ),
            'Test Top Two' => array(
                '3' => 'Test Item Three'
            )
        ));
    }

    public function testOptions() {
        $field = $this->createField();

        $options = $field->getOptions();

        $this->assertEquals(2, count($options));
        $this->assertEquals(2, count($options->shift()->ChildOptions));
        $this->assertEquals(1, count($options->shift()->ChildOptions));
    }

    public function testForTemplate() {
        $field = $this->createField();

        $parser = new CSSContentParser((string) $field->forTemplate());
        $matches = $parser->getBySelector('input');

        $this->assertEquals(3, count($matches));
    }

    public function testDefaultValues() {
        $field = $this->createField();

        $field->setValue(array(
            '1',
            '3'
        ));

        $output = (string) $field->forTemplate();

        $parser = new CSSContentParser($output);
        $matches = $parser->getBySelector('input');

        $checkedCount = 0;

        foreach ($matches as $match) {
            if ($match['checked'] == 'checked') {
                $checkedCount += 1;
            }
        }

        $this->assertEquals(2, $checkedCount);
    }

    public function testValidation() {
        $field = $this->createField();

        $field->setValue(array(
            'false value',
            '3'
        ));

        $exceptionThrown = false;

        try {
            $field->validate(new NestedCheckboxSetFieldTest_Validator());
        } catch (Exception $e) {
            $exceptionThrown = true;
            $this->assertEquals(
                'Please select a value within the list provided. \'false value\' is not a valid option',
                $e->getMessage()
            );
        }

        $this->assertTrue($exceptionThrown, 'Validation should have thrown an error');
    }

    public function testNoValueSubmission() {
        $field = $this->createField();

        $exceptionThrown = false;

        try {
            $field->validate(new NestedCheckboxSetFieldTest_Validator());
        } catch (Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertFalse($exceptionThrown);
    }

    public function testEmptyStringValueSubmission() {
        $field = $this->createField();

        $exceptionThrown = false;

        $field->setValue('');

        try {
            $field->validate(new NestedCheckboxSetFieldTest_Validator());
        } catch (Exception $e) {
            $exceptionThrown = true;
        }

        $this->assertFalse($exceptionThrown);
    }

}

class NestedCheckboxSetFieldTest_Validator extends Validator {

    public function validationError($fieldName, $message, $messageType='') {
        throw new Exception($message);
    }

    public function javascript() {

    }

    public function php($data) {

    }

}