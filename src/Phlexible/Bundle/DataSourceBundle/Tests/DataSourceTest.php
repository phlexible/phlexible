<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\Tests;

use Phlexible\Bundle\DataSourceBundle\Entity\DataSource;

/**
 * DataSource Test
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class DataSourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Set title
     */
    public function testSetTitle()
    {
        // SETUP
        $title = 'mytitle';
        $dataSource = new DataSource();

        // EXERCISE
        $dataSource->setTitle($title);

        // VERIFY
        $this->assertEquals($title, $dataSource->getTitle(), 'title not set correctly');
    }

    /**
     * Set active keys
     */
    public function testSetActiveKeys()
    {
        // SETUP
        $keys = array(
            'id-1' => 'key-1',
            'id-2' => 'key-2',
        );

        $dataSource = new DataSource();

        // EXERCISE
        $dataSource->setValues($keys);

        // VERIFY
        $this->assertEquals($keys, $dataSource->getActiveValuesForLanguage(), 'active keys not set correctly');
    }

    /**
     * Inactive keys are set
     */
    public function testInactiveKeysAreSet()
    {
        // SETUP
        $dataSource     = $this->createDataSourceAlphabet();
        $deactivateKeys = $this->createKeysAlphabet('b', 'd');

        // EXERCISE
        $dataSource->deactivateValuesForLanguage($deactivateKeys);

        // VERIFY
        $this->assertEquals(
            $deactivateKeys,
            $dataSource->getInactiveValuesForLanguage(),
            'inactive keys not set correctly'
        );
    }

    /**
     * Inactive keys are set
     */
    public function testInactiveKeysAreRemovedFromActiveKeys()
    {
        // SETUP
        $dataSource     = $this->createDataSourceAlphabet();
        $deactivateKeys = $this->createKeysAlphabet('c', 'z');
        $expected       = $this->createKeysAlphabet('a', 'b');

        // EXERCISE
        $dataSource->deactivateValuesForLanguage($deactivateKeys);

        // VERIFY
        $this->assertEquals(
            $expected,
            $dataSource->getActiveValuesForLanguage(),
            'inactive keys not removed from active keys'
        );
    }

    /**
     * Deactivate Keys using numeric array
     *
     * @depend testInactiveKeysAreRemovedFromActiveKeys
     * @depend testInactiveKeysAreSet
     */
    public function testDeactivateKeysUsingNumericArray()
    {
        // SETUP
        $dataSource       = $this->createDataSourceAlphabet();
        $deactivateKeys   = array('a', 'b');
        $expectedInactive = $this->createKeysAlphabet('a', 'b');
        $expectedActive   = $this->createKeysAlphabet('c', 'z');

        // EXERCISE
        $dataSource->deactivateValuesForLanguage($deactivateKeys);

        // VERIFY
        $this->assertEquals(
            $expectedActive,
            $dataSource->getActiveValuesForLanguage(),
            'active keys not set correctly after deactivation using numeric array'
        );

        $this->assertEquals(
            $expectedInactive,
            $dataSource->getInactiveValuesForLanguage(),
            'inactive keys not set correctly after deactivation using numeric array'
        );
    }

    /**
     * Activate Keys using numeric array
     */
    public function testActivateKeysUsingNumericArray()
    {
        // SETUP
        $dataSource       = $this->createDataSourceAlphabetWithDeactivatedKeys('a', 'z', 'a', 'e');
        $activateKeys     = array('c', 'd', 'e');
        $expectedInactive = $this->createKeysAlphabet('a', 'b');
        $expectedActive   = $this->createKeysAlphabet('c', 'z');

        // EXERCISE
        $dataSource->activateValuesForLanguage($activateKeys);

        // VERIFY
        $this->assertEquals(
            $expectedActive,
            $dataSource->getActiveValuesForLanguage(),
            'active keys not set correctly after activation using numeric array'
        );

        $this->assertEquals(
            $expectedInactive,
            $dataSource->getInactiveValuesForLanguage(),
            'inactive keys not set correctly after activation using numeric array'
        );
    }

    /**
     * Deactivate Keys does not add new values
     *
     * @depend testInactiveKeysAreRemovedFromActiveKeys
     * @depend testInactiveKeysAreSet
     */
    public function testDeactivateKeysDoesNotAddNewValues()
    {
        // SETUP
        $dataSource       = $this->createDataSourceAlphabet();
        $deactivateKeys   = array_merge($this->createKeysAlphabet('a', 'b'), array('neu' => 'neu'));
        $expectedInactive = $this->createKeysAlphabet('a', 'b');
        $expectedActive   = $this->createKeysAlphabet('c', 'z');

        // EXERCISE
        $dataSource->deactivateValuesForLanguage($deactivateKeys);

        // VERIFY
        $this->assertEquals(
            $expectedActive,
            $dataSource->getActiveValuesForLanguage(),
            'new values added in active values'
        );

        $this->assertEquals(
            $expectedInactive,
            $dataSource->getInactiveValuesForLanguage(),
            'new values added in inactive values'
        );
    }

    /**
     * Remove values from active keys.
     */
    public function testRemoveValuesFromActiveKeys()
    {
        // SETUP
        $dataSource = $this->createDataSourceAlphabet();
        $removeKeys = $this->createKeysAlphabet('a', 'f');
        $expected   = $this->createKeysAlphabet('g', 'z');

        // EXERCISE
        $dataSource->removeValuesForLanguage($removeKeys);

        // VERIFY
        $this->assertEquals(
            $expected,
            $dataSource->getActiveValuesForLanguage(),
            'keys are not removed from active keys'
        );
    }

    /**
     * Remove values from inactive keys.
     */
    public function testRemoveValuesFromInactiveKeys()
    {
        // SETUP
        $dataSource = $this->createDataSourceAlphabetWithDeactivatedKeys();
        $removeKeys = $this->createKeysAlphabet('a', 'f');
        $expected   = $this->createKeysAlphabet('g', 'z');

        // EXERCISE
        $dataSource->removeValuesForLanguage($removeKeys);

        // VERIFY
        $this->assertEquals(
            $expected,
            $dataSource->getInactiveValuesForLanguage(),
            'keys are not removed from inactive keys'
        );
    }

    /**
     * Remove values from keys using numeric array.
     *
     * @depend testRemoveValuesFromActiveKeys
     * @depend testRemoveValuesFromInactiveKeys
     */
    public function testRemoveValuesFromKeysUsingNumericArray()
    {
        // SETUP
        $dataSource       = $this->createDataSourceAlphabetWithDeactivatedKeys('a', 'z', 'a', 'f');
        $removeKeys       = array('e', 'f', 'g', 'h', 'i');
        $expectedActive   = $this->createKeysAlphabet('j', 'z');
        $expectedInactive = $this->createKeysAlphabet('a', 'd');

        // EXERCISE
        $dataSource->removeValuesForLanguage($removeKeys);

        // VERIFY
        $this->assertEquals(
            $expectedActive,
            $dataSource->getActiveValuesForLanguage(),
            'keys are not removed from active keys using numeric array'
        );

        $this->assertEquals(
            $expectedInactive,
            $dataSource->getInactiveValuesForLanguage(),
            'keys are not removed from inactive keys using numeric array'
        );
    }

    /**
     * Create data source 'alphabet'
     *
     * @param string $startChar [Optional] default = 'a'
     * @param string $endChar   [Optional] default = 'z'
     *
     * @return DataSource
     */
    public function createDataSourceAlphabet($startChar = 'a', $endChar = 'z')
    {
        $dataSource = new DataSource();
        $dataSource->setValues($this->createKeysAlphabet($startChar, $endChar));

        return $dataSource;
    }

    /**
     * Create data source 'alphabet'
     *
     * @param string $startActiveChr   [Optional] default = 'a'
     * @param string $endActiveChr     [Optional] default = 'z'
     * @param string $startInactiveChr [Optional] default = 'a'
     * @param string $endInactiveChr   [Optional] default = 'z'
     *
     * @return DataSource
     */
    public function createDataSourceAlphabetWithDeactivatedKeys($startActiveChr   = 'a',
                                                                $endActiveChr     = 'z',
                                                                $startInactiveChr = 'a',
                                                                $endInactiveChr   = 'z')
    {
        $dataSource = $this->createDataSourceAlphabet($startActiveChr, $endActiveChr);
        $dataSource->deactivateValuesForLanguage($this->createKeysAlphabet($startInactiveChr, $endInactiveChr));

        return $dataSource;
    }

    /**
     * Create a keys array
     *
     * @param string $startChar [Optional] default = 'a'
     * @param string $endChar   [Optional] default = 'z'
     *
     * @return array
     */
    public function createKeysAlphabet($startChar = 'a', $endChar = 'z')
    {
        $startOrd = ord($startChar);
        $endOrd   = ord($endChar);

        $keys = array();
        for ($i = $startOrd; $i <= $endOrd; ++$i) {
            $c = chr($i);
            $keys["id-$c"] = $c;
        }

        return $keys;
    }
}
