<?php

/**
 */
class MWF_Core_DataSources_DataSourceTest extends PHPUnit_Framework_TestCase
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
     * Set title
     */
    public function testSetTitle()
    {
        // SETUP
        $title = 'mytitle';
        $dataSource = new MWF_Core_DataSources_DataSource();

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

        $dataSource = new MWF_Core_DataSources_DataSource();

        // EXERCISE
        $dataSource->setKeys($keys);

        // VERIFY
        $this->assertEquals($keys, $dataSource->getActiveKeys(), 'active keys not set correctly');
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
        $dataSource->deactivateKeys($deactivateKeys);

        // VERIFY
        $this->assertEquals(
            $deactivateKeys,
            $dataSource->getInactiveKeys(),
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
        $dataSource->deactivateKeys($deactivateKeys);

        // VERIFY
        $this->assertEquals(
            $expected,
            $dataSource->getActiveKeys(),
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
        $dataSource->deactivateKeys($deactivateKeys);

        // VERIFY
        $this->assertEquals(
            $expectedActive,
            $dataSource->getActiveKeys(),
            'active keys not set correctly after deactivation using numeric array'
        );

        $this->assertEquals(
            $expectedInactive,
            $dataSource->getInactiveKeys(),
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
        $dataSource->activateKeys($activateKeys);

        // VERIFY
        $this->assertEquals(
            $expectedActive,
            $dataSource->getActiveKeys(),
            'active keys not set correctly after activation using numeric array'
        );

        $this->assertEquals(
            $expectedInactive,
            $dataSource->getInactiveKeys(),
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
        $dataSource->deactivateKeys($deactivateKeys);

        // VERIFY
        $this->assertEquals(
            $expectedActive,
            $dataSource->getActiveKeys(),
            'new values added in active values'
        );

        $this->assertEquals(
            $expectedInactive,
            $dataSource->getInactiveKeys(),
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
        $dataSource->removeKeys($removeKeys);

        // VERIFY
        $this->assertEquals(
            $expected,
            $dataSource->getActiveKeys(),
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
        $dataSource->removeKeys($removeKeys);

        // VERIFY
        $this->assertEquals(
            $expected,
            $dataSource->getInactiveKeys(),
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
        $dataSource->removeKeys($removeKeys);

        // VERIFY
        $this->assertEquals(
            $expectedActive,
            $dataSource->getActiveKeys(),
            'keys are not removed from active keys using numeric array'
        );

        $this->assertEquals(
            $expectedInactive,
            $dataSource->getInactiveKeys(),
            'keys are not removed from inactive keys using numeric array'
        );
    }

    /**
     * Create data source 'alphabet'
     *
     * @param string $startChar [Optional] default = 'a'
     * @param string $endChar   [Optional] default = 'z'
     *
     * @return MWF_Core_DataSources_DataSource
     */
    public function createDataSourceAlphabet($startChar = 'a', $endChar = 'z')
    {
        $dataSource = new MWF_Core_DataSources_DataSource();
        $dataSource->setKeys($this->createKeysAlphabet($startChar, $endChar));

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
     * @return MWF_Core_DataSources_DataSource
     */
    public function createDataSourceAlphabetWithDeactivatedKeys($startActiveChr   = 'a',
                                                                $endActiveChr     = 'z',
                                                                $startInactiveChr = 'a',
                                                                $endInactiveChr   = 'z')
    {
        $dataSource = $this->createDataSourceAlphabet($startActiveChr, $endActiveChr);
        $dataSource->deactivateKeys($this->createKeysAlphabet($startInactiveChr, $endInactiveChr));

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
        for ($i = $startOrd; $i <= $endOrd; ++$i)
        {
            $c = chr($i);
            $keys["id-$c"] = $c;
        }

        return $keys;
    }
}
