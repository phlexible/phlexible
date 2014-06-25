<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Context;

/**
 * Context manager
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class ContextManager
{
    /**
     * @var MWF_Db_Pool
     */
    protected $_dbPool;

    /**
     * @var boolean
     */
    protected $_useContext;

    /**
     * @var array
     */
    protected $_contextCountries;

    /**
     * Constructor
     *
     * @param MWF_Db_Pool $dbPool
     * @param boolean     $useContext
     * @param array       $contextCountries
     */
    public function __construct(MWF_Db_Pool $dbPool,
                                $useContext,
                                array $contextCountries)
    {
        $this->_dbPool     = $dbPool;
        $this->_useContext = $useContext;
        $this->_contextCountries = $contextCountries;
    }

    /**
     * Is context usage activated in registry and configured properly in configuration file.
     *
     * @return boolean
     */
    public function useContext()
    {
        if ($this->_useContext)
        {
            if (empty($this->_contextCountries))
            {
                $msg = "Context is activated in config ('elements.context.enabled')"
                     . ", but 'elements.context.countries' is missing in config file.";

                $this->_useContext = false;
            }
        }

        return $this->_useContext;
    }

    /**
     * Fetch available countries from registry.
     *
     * @return array
     */
    public function getAllCountries()
    {
        $countries = $this->useContext()
            ? $this->_contextCountries
            : array();

        return $countries;
    }

    /**
     * Fetch active countries by tid from database.
     *
     * @return array
     */
    public function getActiveCountriesByTid($tid)
    {
        $db = $this->_dbPool->read;

        $select = $db->select()
            ->from($db->prefix . 'element_tree_context', 'context')
            ->where('tid = ?', (integer) $tid);

        $activeCountries = $db->fetchCol($select);

        /*
         * TODO can this be removed?
         *
        if (!count($activeContextCountries) && $language && isset($config->context->defaults))
        {
            $contextDefaults  = $config->context->defaults->toArray();

            foreach ($contextDefaults as $contextCountry => $contextLanguages)
            {
                if (in_array($language, $contextLanguages))
                {
                    $activeContextCountries[] = $contextCountry;
                }
            }
        }
        */

        return $activeCountries;
    }

    /**
     * Fetch active countries by tid from database.
     *
     * @return array
     */
    public function getActiveCountriesByTeaserId($teaserId)
    {
        $db = $this->_dbPool->read;

        $select = $db->select()
            ->from($db->prefix . 'element_tree_teasers_context', 'context')
            ->where('teaser_id = ?', (integer) $teaserId);

        $activeCountries = $db->fetchCol($select);

        /*
         * TODO can this be removed?
         *
        if (!count($activeContextCountries) && $language && isset($config->context->defaults))
        {
            $contextDefaults  = $config->context->defaults->toArray();

            foreach ($contextDefaults as $contextCountry => $contextLanguages)
            {
                if (in_array($language, $contextLanguages))
                {
                    $activeContextCountries[] = $contextCountry;
                }
            }
        }
        */

        return $activeCountries;
    }
}
