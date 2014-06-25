<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Exception.php 2943 2007-04-18 09:00:40Z swentz $
 */

/**
 * Makeweb Content Element Version Manager
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Element_Version_MetaSet_Manager
{
    /**
     * @var Makeweb_Elements_Element_Version_Manager
     */
    protected static $_instance = null;

    /**
     * Constructor
     * Protected, use getInstance()
     *
     * @see Makeweb_Elements_Element_Version_MetaSet_Manager::getInstance()
     */
    protected function __construct()
    {

    }

    /**
     * Returns the singleton instance
     *
     * @return Makeweb_Elements_Element_Version_MetaSet_Manager
     */
    public static function getInstance()
    {
        if (self::$_instance === null)
        {
            self::$_instance = new Makeweb_Elements_Element_Version_MetaSet_Manager();
        }

        return self::$_instance;
    }

    /**
     * Returns metasearch keys
     *
     * @return array $result
     */
    public function getKeys()
    {
        $result = array();
        try
        {
            $db = MWF_Registry::getContainer()->dbPool->default;

            $select = $db->select()
                         ->from($db->prefix.'element_version_metaset_items', array('key'))
                         ->group($db->prefix.'element_version_metaset_items.key')
                         ->order('key');

            $result = $db->fetchCol($select);

        }
        catch (Zend_Db_Exception $e)
        {
            MWF_Log::exception($e);
        }
        return $result;
    }

    /**
     * Returns unique meta key values as array
     *
     * @param string $key
     * @return array $values
     */
    public function getMetaKeyValues($key, $language = null)
    {
        $values = array();
        try
        {
            $db = MWF_Registry::getContainer()->dbPool->default;

            $select = $db->select()->distinct()
                ->from(
                    array('evmi' => $db->prefix.'element_version_metaset_items'),
                    array('value')
                )
                #->join(
                #    array('vlev' => $db->prefix.'element_tree_online'),
                #    'evmi.eid = vlev.eid AND evmi.version = vlev.version AND evmi.language = vlev.language',
                #    array()
                #)
                ->where('evmi.key = ?', array($key));

            if (null !== $language)
            {
                $select->where('evmi.language = ?', $language);
            }

            $result = $db->fetchCol($select);
            $tValues = array();
            foreach ($result as $value)
            {
                $tValues = explode(',', $value);
                foreach ($tValues as $tValue)
                {
                    $tValue = trim($tValue);
                    if (!empty($tValue))
                    {
                        $values[] = $tValue;
                    }
                }
            }

            $values = array_unique($values);
            asort($values);
        }
        catch (Zend_Db_Exception $e)
        {
            MWF_Log::exception($e);
        }

        return $values;
    }
}
