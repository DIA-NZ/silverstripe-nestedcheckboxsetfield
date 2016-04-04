<?php

class NestedCheckboxSetFieldSourceGeneratorTest extends SapphireTest {

    protected static $fixture_file = 'nestedcheckboxsetfield/tests/fixtures/NestedCheckboxSetFieldSourceGeneratorTest.yml';

    protected $extraDataObjects = array(
        'NestedCheckboxSetParent',
        'NestedCheckboxSetChild'
    );

    public function testGenerate() {
        $fieldSource = NestedCheckboxSetFieldSourceGenerator::generate(
            NestedCheckboxSetParent::get(),
            function ($parentItem) {
                return $parentItem->Children();
            }
        );

        $this->assertInternalType('array', $fieldSource);
        $this->assertEquals(2, count($fieldSource));
        $this->assertEquals(2, count($fieldSource['Parent 1']));
        $this->assertEquals(3, count($fieldSource['Parent 2']));
    }

}

class NestedCheckboxSetParent extends DataObject implements TestOnly {

    private static $db = array(
        'Title' => 'Varchar'
    );

    private static $has_many = array(
        'Children' => 'NestedCheckboxSetChild'
    );

}

class NestedCheckboxSetChild extends DataObject implements TestOnly {

    private static $db = array(
        'Title' => 'Varchar'
    );

    private static $has_one = array(
        'Parent' => 'NestedCheckboxSetParent'
    );

}
