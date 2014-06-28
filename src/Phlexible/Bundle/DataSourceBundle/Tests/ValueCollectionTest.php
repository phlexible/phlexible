<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Tests;

use Phlexible\Bundle\DataSourceBundle\Value\ValueCollection;

class MWF_Core_DataSources_Value_CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    /**
     * toArray() returns empty array for autoinitialized collection
     */
    public function testToArrayReturnsEmptyForAutoInitializedCollection()
    {
        // EXERCISE
        $collection = new ValueCollection();

        // VERIFY
        $this->assertEquals(
            array(),
            $collection->toArray(),
            'autoinitialized collection is not an empty array'
        );
    }

    /**
     * toArray() returns array passed in constructor.
     */
    public function testToArrayReturnsArrayPassedInConstructor()
    {
        // SETUP
        $values = array(
            'id1' => 'key1',
            'id2' => 'key2',
        );

        // EXERCISE
        $collection = new ValueCollection($values);

        // VERIFY
        $this->assertEquals($values, $collection->toArray(), 'value array modified');
    }

    /**
     * toArray() returns array passed in setValues().
     */
    public function testToArrayReturnsArrayPassedInSetValues()
    {
        // SETUP
        $collection = new ValueCollection();

        $values = array(
            'id1' => 'key1',
            'id2' => 'key2',
        );

        // EXERCISE
        $collection->setValues($values);

        // VERIFY
        $this->assertEquals($values, $collection->toArray(), 'value array modified');
    }

    /**
     * removeByKey() removes correct entries.
     */
    public function testRemoveByKeyRemovesCorrectEntries()
    {
        // SETUP
        $values = array(
            'id1' => 'key1',
            'id2' => 'key2',
            'id3' => 'key3',
        );

        $removeKeys = array(
            'key2',
            'key3',
        );

        $expected = array(
            'id1' => 'key1',
        );

        $collection = new ValueCollection();
        $collection->setValues($values);

        // EXERCISE
        $collection->removeValuesByKey($removeKeys);

        // VERIFY
        $this->assertEquals(
            $expected,
            $collection->toArray(),
            'removeValuesByKey() removes incorrect values'
        );
    }

    /**
     * holdByKey() holds correct entries.
     */
    public function testHoldByKeyHoldsCorrectEntries()
    {
        // SETUP
        $values = array(
            'id1' => 'key1',
            'id2' => 'key2',
            'id3' => 'key3',
        );

        $holdKeys = array(
            'key2',
        );

        $expected = array(
            'id2' => 'key2',
        );

        $collection = new ValueCollection();
        $collection->setValues($values);

        // EXERCISE
        $collection->holdValuesByKey($holdKeys);

        // VERIFY
        $this->assertEquals(
            $expected,
            $collection->toArray(),
            'holdValuesByKey() holds incorrect values'
        );
    }

    /**
     * removeById() removes correct entries.
     */
    public function testRemoveByIdRemovesCorrectEntries()
    {
        // SETUP
        $values = array(
            'id1' => 'key1',
            'id2' => 'key2',
            'id3' => 'key3',
        );

        $removeIds = array(
            'id2',
            'id3',
        );

        $expected = array(
            'id1' => 'key1',
        );

        $collection = new ValueCollection();
        $collection->setValues($values);

        // EXERCISE
        $collection->removeValuesById($removeIds);

        // VERIFY
        $this->assertEquals(
            $expected,
            $collection->toArray(),
            'removeValuesByKey() removes incorrect values'
        );
    }
}
