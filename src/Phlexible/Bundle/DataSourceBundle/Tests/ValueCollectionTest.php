<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Tests;

use Phlexible\Bundle\DataSourceBundle\Model\ValueCollection;

/**
 * DataSource Test
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
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
        $collection->removeValues($removeKeys);

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
        $collection->holdValues($holdKeys);

        // VERIFY
        $this->assertEquals(
            $expected,
            $collection->toArray(),
            'holdValuesByKey() holds incorrect values'
        );
    }
}
