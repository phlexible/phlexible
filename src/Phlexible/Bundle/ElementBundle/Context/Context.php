<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category   MAKEweb
 * @package    Makeweb_Elements
 * @copyright  Copyright (c) 2010 brainbits GmbH (http://www.brainbits.net)
 * @version    $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

/**
 * Elements Context
 *
 * @category   MAKEweb
 * @package    Makeweb_Elements
 * @author     Stephan Wentz <sw@brainbits.net>
 * @copyright  Copyright (c) 2010 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Context
{
    const NO_COUNTRY = -1;
    const GLOBAL_COUNTRY = 'global';

    /**
     * @var string
     */
    protected $_country = null;

    /**
     * @var bool
     */
    protected $_isPreview = null;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * @var Zend_Config
     */
    protected $_config = null;

    /**
     * @var array
     */
    protected $_languageMap = array();

    /**
     * @var array
     */
    protected $_defaults = array();

    /**
     * @var array
     */
    protected $_urls = array();

    /**
     * @var array
     */
    protected $_reverseDefaults = null;

    /**
     * @param bool $isPreview
     */
    public function __construct($isPreview)
    {
        $this->_isPreview = $isPreview;

        $container = MWF_Registry::getContainer();
        $this->_db       = $container->dbPool->read;
        $this->_config   = $container->config;

        if (isset($this->_config->context->defaults))
        {
            $this->_defaults = $this->_config->context->defaults->toArray();
        }
        if (isset($this->_config->context->urls))
        {
            $this->_urls = $this->_config->context->urls->toArray();
        }
        $this->_countries = $this->_config->context->countries->toArray();
    }

    /**
     * Determine country
     *
     * @return string
     */
    protected function _determineCountry()
    {
        $country = self::NO_COUNTRY;

        $url = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';

        if (array_key_exists($url, $this->_urls))
        {
            $country = $this->_urls[$_SERVER['SERVER_NAME']];
        }

        return $country;
    }

    /**
     * Get country of context
     *
     * @return string
     */
    public function getCountry()
    {
        if ($this->_country === null)
        {
            $this->setCountry($this->_determineCountry());
        }

        return $this->_country;
    }

    /**
     * Set the country of context
     *
     * @param  string $country
     * @throws Makeweb_Elements_Context_Exception
     */
    public function setCountry($country)
    {
        if ($country == self::GLOBAL_COUNTRY)
        {
            $this->_country = $country;
        }
        elseif (!$country || ($country !== self::NO_COUNTRY && !array_key_exists($country, $this->_countries)))
        {
            throw new Makeweb_Elements_Context_Exception('No country found for "' . $country . '".');
        }

        $this->_country = $country;
    }

    /**
     * Is this tid relevant for this language?
     *
     * @param int          $tid
     * @param array|string $languages
     *
     * @return bool
     */
    public function isRelevantForTid($tid, $languages)
    {
        if (!is_array($languages))
        {
            $languages = array($languages);
        }

        $languagesForTid = $this->getLanguagesForTid($tid);

        foreach ($languages as $language)
        {
            if (in_array($language, $languagesForTid))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Is this teaserid relevant for this language?
     *
     * @param int          $teaserId
     * @param array|string $languages
     *
     * @return bool
     */
    public function isRelevantForTeaserId($teaserId, $languages)
    {
        if (!is_array($languages))
        {
            $languages = array($languages);
        }

        $languagesForTeaserId = $this->getLanguagesForTeaserId($teaserId);

        foreach ($languages as $language)
        {
            if (in_array($language, $languagesForTeaserId))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Get default language for current context
     *
     * @return string
     */
    public function getDefaultLanguage()
    {
        $country = $this->getCountry();

        $defaultLanguages = $this->_defaults[$country];

        return current($defaultLanguages);
    }

    /**
     * Return languages for this tid
     *
     * @param int $tid
     *
     * @return array
     */
    public function getLanguagesForTid($tid)
    {
        if (!empty($this->_languageMap['treenode_' . $tid]))
        {
            return $this->_languageMap['treenode_' . $tid];
        }

        $country = $this->getCountry();

        if (!$this->_isPreview)
        {
            $select = $this->_db->select()
                ->from($this->_db->prefix . 'element_tree_context', 'context')
                ->where('tid = ?', $tid);

            $available = $this->_db->fetchCol($select);

            if (count($available) && !in_array($country, $available))
            {
                return array();
            }

            $select = $this->_db->select()
                ->from($this->_db->prefix . 'element_tree_online', 'language')
                ->where('tree_id = ?', $tid);

            $onlineLanguages = $this->_db->fetchCol($select);

            if (count($this->_defaults))
            {
                if (self::GLOBAL_COUNTRY === $country)
                {
                    $languages = $onlineLanguages;
                }
                else
                {
                    $defaultLanguages = $this->_defaults[$country];

                    //$languages = array_unique($languages);
                    $languages = array_intersect($defaultLanguages, $onlineLanguages);
                }

            }
            else
            {
                $languages = $onlineLanguages;
            }
        }
        else
        {
            $languages = array();

            if (!empty($this->_defaults))
            {
                foreach ($this->_defaults as $l)
                {
                    $languages = array_merge($languages, $l);
                }
            }
            else
            {
               $languages = explode(',', MWF_Registry::getContainer()->getParam(':frontend.languages.available'));
            }

            $languages = array_unique($languages);
        }

        $this->_languageMap['treenode_' . $tid] = $languages;

        return $languages;
    }

    /**
     * Return languages for this teaserid
     *
     * @param int $tid
     *
     * @return array
     */
    public function getLanguagesForTeaserId($teaserId)
    {
        if (!empty($this->_languageMap['teaser_' . $teaserId]))
        {
            return $this->_languageMap['teaser_' . $teaserId];
        }

        $country = $this->getCountry();

        if (!$this->_isPreview)
        {
            $select = $this->_db->select()
                ->from($this->_db->prefix . 'element_tree_teasers_context', 'context')
                ->where('teaser_id = ?', $teaserId);

            $available = $this->_db->fetchCol($select);

            if (count($available) && !in_array($country, $available))
            {
                return array();
            }

            $select = $this->_db->select()
                ->from($this->_db->prefix . 'element_tree_teasers_online', 'language')
                ->where('teaser_id = ?', $teaserId);

            $onlineLanguages = $this->_db->fetchCol($select);

            if (count($this->_defaults))
            {
                if (self::GLOBAL_COUNTRY === $country)
                {
                    $languages = $onlineLanguages;
                }
                else
                {
                    $defaultLanguages = $this->_defaults[$country];

                    //$languages = array_unique($languages);
                    $languages = array_intersect($defaultLanguages, $onlineLanguages);
                }
            }
            else
            {
                $languages = $onlineLanguages;
            }
        }
        else
        {
            $languages = array();

            if (!empty($this->_defaults))
            {
                foreach ($this->_defaults as $l)
                {
                    $languages = array_merge($languages, $l);
                }
            }
            else
            {
               $languages = explode(',', MWF_Registry::getContainer()->getParam(':frontend.languages.available'));
            }

            $languages = array_unique($languages);
        }

        $this->_languageMap['teaser_' . $teaserId] = $languages;

        return $languages;
    }

    /**
     * Get reversed country/language values from defauls
     *
     * @return array
     */
    protected function _getReverseDefaults()
    {
        if ($this->_reverseDefaults === null)
        {
            $reverseDefaults = array();

            foreach ($this->_defaults as $country => $languages)
            {
                foreach ($languages as $language)
                {
                    if (!array_key_exists($language, $reverseDefaults))
                    {
                        $reverseDefaults[$language] = array();
                    }

                    if (!in_array($country, $reverseDefaults[$language]))
                    {
                        $reverseDefaults[$language][] = $country;
                    }
                }
            }

            $this->_reverseDefaults = $reverseDefaults;
        }

        return $this->_reverseDefaults;
    }

    /**
     * Return languages for this tid
     *
     * @param int    $tid
     * @param string $language
     *
     * @return array|null array is empty page is globally available
     *                    and null if language/context combination is invalid
     */
    public function getCountriesForTidAndLanguage($tid, $language)
    {
        $select = $this->_db->select()
            ->from($this->_db->prefix . 'element_tree_context', 'context')
            ->where('tid = ?', $tid);

        $countries = $this->_db->fetchCol($select);

        if (count($this->_defaults))
        {
            $reverseDefaults = $this->_getReverseDefaults();

            if (!array_key_exists($language, $reverseDefaults))
            {
                throw new Makeweb_Elements_Context_Exception(
                    'No country found for language "' . $language . '".'
                );
            }

            $defaultCountries = $reverseDefaults[$language];

            if (count($countries))
            {
                $countries = array_intersect($countries, $defaultCountries);
            }
            else
            {
                $countries = $defaultCountries;
            }

            // if language/context combination is invalid
            // -> return null
            if (!count($countries))
            {
                return null;
            }
        }

        return $countries;
    }
}
