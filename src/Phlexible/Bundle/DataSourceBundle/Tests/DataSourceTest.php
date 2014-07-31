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
        $title = 'mytitle';

        $dataSource = new DataSource();
        $dataSource->setTitle($title);

        $this->assertEquals($title, $dataSource->getTitle());
    }

    /**
     * Set active keys
     */
    public function testSetActiveKeys()
    {
        $keys = array('key-1', 'key-2');

        $dataSource = new DataSource();
        $dataSource->setValues('de', $keys);

        $this->assertEquals($keys, $dataSource->getActiveValuesForLanguage('de'));
        $this->assertEquals(array(), $dataSource->getActiveValuesForLanguage('en'));
    }

    /**
     * Inactive keys are set
     */
    public function testInactiveKeysAreSet()
    {
        $deactivateKeys = $this->createKeysAlphabet('b', 'd');

        $dataSource = $this->createDataSourceAlphabet();
        $dataSource->deactivateValuesForLanguage('de', $deactivateKeys);

        $this->assertEquals($deactivateKeys, $dataSource->getInactiveValuesForLanguage('de'));
        $this->assertEquals(array(), $dataSource->getInactiveValuesForLanguage('en'));
    }

    /**
     * Inactive keys are set
     */
    public function testInactiveKeysAreRemovedFromActiveKeys()
    {
        $deactivateKeys = $this->createKeysAlphabet('c', 'z');
        $expected       = $this->createKeysAlphabet('a', 'b');

        $dataSource = $this->createDataSourceAlphabet();
        $dataSource->deactivateValuesForLanguage('de', $deactivateKeys);

        $this->assertEquals($expected, $dataSource->getActiveValuesForLanguage('de'));
        $this->assertEquals(array(), $dataSource->getActiveValuesForLanguage('en'));
    }

    /**
     * Deactivate Keys using numeric array
     *
     * @depend testInactiveKeysAreRemovedFromActiveKeys
     * @depend testInactiveKeysAreSet
     */
    public function testDeactivateKeysUsingNumericArray()
    {
        $deactivateKeys   = array('a', 'b');
        $expectedInactive = $this->createKeysAlphabet('a', 'b');
        $expectedActive   = $this->createKeysAlphabet('c', 'z');

        $dataSource = $this->createDataSourceAlphabet();
        $dataSource->deactivateValuesForLanguage('de', $deactivateKeys);

        $this->assertEquals($expectedActive, $dataSource->getActiveValuesForLanguage('de'));
        $this->assertEquals($expectedInactive, $dataSource->getInactiveValuesForLanguage('de'));
    }

    /**
     * Activate Keys using numeric array
     */
    public function testActivateKeysUsingNumericArray()
    {
        $activateKeys     = array('c', 'd', 'e');
        $expectedInactive = $this->createKeysAlphabet('a', 'b');
        $expectedActive   = $this->createKeysAlphabet('c', 'z');

        $dataSource = $this->createDataSourceAlphabetWithDeactivatedKeys('a', 'z', 'a', 'e');
        $dataSource->activateValuesForLanguage('de', $activateKeys);

        $this->assertEquals($expectedActive, $dataSource->getActiveValuesForLanguage('de'));
        $this->assertEquals($expectedInactive, $dataSource->getInactiveValuesForLanguage('de'));
    }

    /**
     * Deactivate Keys does not add new values
     *
     * @depend testInactiveKeysAreRemovedFromActiveKeys
     * @depend testInactiveKeysAreSet
     */
    public function testDeactivateKeysDoesNotAddNewValues()
    {
        $deactivateKeys   = array_merge($this->createKeysAlphabet('a', 'b'), array('neu' => 'neu'));
        $expectedInactive = $this->createKeysAlphabet('a', 'b');
        $expectedActive   = $this->createKeysAlphabet('c', 'z');

        $dataSource = $this->createDataSourceAlphabet();
        $dataSource->deactivateValuesForLanguage('de', $deactivateKeys);

        $this->assertEquals($expectedActive, $dataSource->getActiveValuesForLanguage('de'));
        $this->assertEquals($expectedInactive, $dataSource->getInactiveValuesForLanguage('de'));
    }

    /**
     * Remove values from active keys.
     */
    public function testRemoveValuesFromActiveKeys()
    {
        $removeKeys = $this->createKeysAlphabet('a', 'f');
        $expected   = $this->createKeysAlphabet('g', 'z');

        $dataSource = $this->createDataSourceAlphabet();
        $dataSource->removeValuesForLanguage('de', $removeKeys);

        $this->assertEquals( $expected, $dataSource->getActiveValuesForLanguage('de'));
    }

    /**
     * Remove values from inactive keys.
     */
    public function testRemoveValuesFromInactiveKeys()
    {
        $removeKeys = $this->createKeysAlphabet('a', 'f');
        $expected   = $this->createKeysAlphabet('g', 'z');

        $dataSource = $this->createDataSourceAlphabetWithDeactivatedKeys();
        $dataSource->removeValuesForLanguage('de', $removeKeys);

        $this->assertEquals($expected, $dataSource->getInactiveValuesForLanguage('de'));
    }

    /**
     * Remove values from keys using numeric array.
     *
     * @depend testRemoveValuesFromActiveKeys
     * @depend testRemoveValuesFromInactiveKeys
     */
    public function testRemoveValuesFromKeysUsingNumericArray()
    {
        $language         = 'de';
        $removeKeys       = array('e', 'f', 'g', 'h', 'i');
        $expectedActive   = $this->createKeysAlphabet('j', 'z');
        $expectedInactive = $this->createKeysAlphabet('a', 'd');

        $dataSource = $this->createDataSourceAlphabetWithDeactivatedKeys('a', 'z', 'a', 'f', $language);
        $dataSource->removeValuesForLanguage($language, $removeKeys);

        $this->assertEquals($expectedActive, $dataSource->getActiveValuesForLanguage($language));
        $this->assertEquals($expectedInactive, $dataSource->getInactiveValuesForLanguage($language));
    }

    /**
     * Create data source 'alphabet'
     *
     * @param string $startChar [Optional] default = 'a'
     * @param string $endChar   [Optional] default = 'z'
     * @param string $language  [Optional] default = 'de'
     *
     * @return DataSource
     */
    public function createDataSourceAlphabet($startChar = 'a', $endChar = 'z', $language = 'de')
    {
        $dataSource = new DataSource();
        $dataSource->setValues($language, $this->createKeysAlphabet($startChar, $endChar));

        return $dataSource;
    }

    /**
     * Create data source 'alphabet'
     *
     * @param string $startActiveChr   [Optional] default = 'a'
     * @param string $endActiveChr     [Optional] default = 'z'
     * @param string $startInactiveChr [Optional] default = 'a'
     * @param string $endInactiveChr   [Optional] default = 'z'
     * @param string $language         [Optional] default = 'de'
     *
     * @return DataSource
     */
    public function createDataSourceAlphabetWithDeactivatedKeys($startActiveChr   = 'a',
                                                                $endActiveChr     = 'z',
                                                                $startInactiveChr = 'a',
                                                                $endInactiveChr   = 'z',
                                                                $language = 'de')
    {
        $dataSource = $this->createDataSourceAlphabet($startActiveChr, $endActiveChr, $language);
        $dataSource->deactivateValuesForLanguage($language, $this->createKeysAlphabet($startInactiveChr, $endInactiveChr));

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
            $keys[] = $c;
        }

        return $keys;
    }
}
