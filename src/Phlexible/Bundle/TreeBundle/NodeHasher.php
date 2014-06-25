<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Exception.php 4161 2008-03-11 18:37:34Z swentz $
 */

/**
 * Makeweb System Elements Element Hash
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Tree_Node_Hash extends Makeweb_Elements_Element_Hash
{
    public function getHashByTid($tid, $language, $version)
    {
        $identifier = $tid . '__' . $language . '__' . $version;

        if (!empty($this->_hashes[$identifier]))
        {
            return $this->_hashes[$identifier];
        }

        $select = $this->_db->select()
            ->from($this->_db->prefix . 'element_tree_hash', 'hash')
            ->where('tid = ?', $tid)
            ->where('language = ?', $language)
            ->where('version = ?', $version)
            ->limit(1);

        $hash = $this->_db->fetchOne($select);

        if (!$hash)
        {
            $values = $this->_getHashValuesByTid($tid, $language, $version);
            $hash   = $this->_createHashFromValues($values);

            $insertData = array(
                'tid'      => $tid,
                'language' => $language,
                'version'  => $version,
                'hash'     => $hash,
                'debug'    => print_r($values, 1),
            );

            $this->_db->insert($this->_db->prefix . 'element_tree_hash', $insertData);
        }

        $this->_hashes[$identifier] = $hash;

        return $hash;
    }

    public function getHashValuesByTid($tid, $language, $version)
    {
        $values = $this->_getHashValuesByTid($tid, $language, $version);
        $hash   = $this->_createHashFromValues($values);

        return array('values' => $values, 'hash' => $hash);
    }

    protected function _getHashValuesByTid($tid, $language, $version)
    {
        $selectEid = $this->_db->select()
            ->from($this->_db->prefix . 'element_tree', array('eid'))
            ->where('id = ?', $tid)
            ->limit(1);
#echo $selectEid.PHP_EOL;

        $eid = $this->_db->fetchOne($selectEid);

        $selectPage = $this->_db->select()
            ->from($this->_db->prefix . 'element_tree_page', array('navigation', 'restricted', 'disable_cache', 'cache_lifetime', 'code', 'https'))
            ->where('tree_id = ?', $tid)
            ->where('version = ?', $version)
            ->limit(1);
#echo $selectPage.PHP_EOL;

        $page = $this->_db->fetchRow($selectPage);

        $values = $this->_getHashValuesByEid($eid, $language, $version);
        $values['page'] = $page;

        return $values;
    }
}