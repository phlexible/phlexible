<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Element;

/**
 * Element hasher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class ElementHasher
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * @var array
     */
    protected $_hashes = array();

    /**
     * Constructor
     *
     * @param MWF_Db_Pool $dbPool
     */
    public function __construct(MWF_Db_Pool $dbPool)
    {
        $this->_db = $dbPool->write;
    }

    protected function _getHashValuesByEid($eid, $language, $version)
    {
        $selectElementType = $this->_db->select()
            ->from($this->_db->prefix . 'element_version', array('element_type_id', 'element_type_version'))
            ->where('eid = ?', $eid)
            ->where('version = ?', $version)
            ->limit(1);
        //echo $selectEid.PHP_EOL;

        $elementType = $this->_db->fetchOne($selectElementType);

        $selectMeta = $this->_db->select()
            ->from($this->_db->prefix . 'element_version_metaset_items', array('value'))
            ->where('eid = ?', $eid)
            ->where('language = ?', $language)
            ->where('version = ?', $version)
            ->where('value IS NOT NULL')
            ->where('value != ""');
        //echo $selectMeta.PHP_EOL;

        $meta = $this->_db->fetchCol($selectMeta);
        sort($meta);

        $selectContext = $this->_db->select()
            ->from($this->_db->prefix . 'element_version_metaset_items', array('value'))
            ->where('eid = ?', $eid)
            ->where('language = ?', $language)
            ->where('version = ?', $version)
            ->where('value IS NOT NULL')
            ->where('value != ""');
        //echo $selectMeta.PHP_EOL;

        $context = $this->_db->fetchCol($selectContext);
        sort($context);

        $selectContent = $this->_db->select()
            ->from($this->_db->prefix . 'element_data_language', array('content'))
            ->where('eid = ?', $eid)
            ->where('language = ?', $language)
            ->where('version = ?', $version)
            ->where('content IS NOT NULL')
            ->where('content != ""');
        //echo $selectContent.PHP_EOL;

        $content = $this->_db->fetchCol($selectContent);
        sort($content);

        $values = array(
            'eid'         => $eid,
            'elementType' => $elementType,
            'meta'        => $meta,
            'context'     => $context,
            'content'     => $content,
        );

        return $values;
    }

    protected function _createHashFromValues(array $values)
    {
        $hash = md5(serialize($values));

        return $hash;
    }
}