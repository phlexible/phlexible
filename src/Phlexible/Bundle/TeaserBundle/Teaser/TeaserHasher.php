<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Teaser;

use Phlexible\Bundle\ElementBundle\Element\ElementHasher;

/**
 * Teaserh hasher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserHasher extends ElementHasher
{
    public function getHashByTeaserId($teaserId, $language, $version)
    {
        $identifier = $teaserId . '__' . $language . '__' . $version;

        if (!empty($this->_hashes[$identifier])) {
            return $this->_hashes[$identifier];
        }

        $select = $this->_db->select()
            ->from($this->_db->prefix . 'element_tree_teasers_hash', 'hash')
            ->where('teaser_id = ?', $teaserId)
            ->where('language = ?', $language)
            ->where('version = ?', $version)
            ->limit(1);

        $hash = $this->_db->fetchOne($select);

        if (!$hash) {
            $values = $this->_getHashValuesByTeaserId($teaserId, $language, $version);
            $hash = $this->_createHashFromValues($values);

            $insertData = array(
                'teaser_id' => $teaserId,
                'language'  => $language,
                'version'   => $version,
                'hash'      => $hash,
                'debug'     => print_r($values, 1),
            );

            $this->_db->insert($this->_db->prefix . 'element_tree_teasers_hash', $insertData);
        }

        $this->_hashes[$identifier] = $hash;

        return $hash;
    }

    public function getHashValuesByTeaserId($teaserId, $language, $version)
    {
        $values = $this->_getHashValuesByTid($teaserId, $language, $version);
        $hash = $this->_createHashFromValues($values);

        return array('values' => $values, 'hash' => $hash);
    }

    protected function _getHashValuesByTeaserId($teaserId, $language, $version)
    {
        $selectTeaser = $this->_db->select()
            ->from($this->_db->prefix . 'element_tree_teasers', array('teaser_eid', 'template_id'))
            ->where('id = ?', $teaserId)
            ->limit(1);
        #echo $selectPage.PHP_EOL;

        $teaserRow = $this->_db->fetchRow($selectTeaser);
        $eid = $teaserRow['teaser_eid'];
        $templateId = $teaserRow['template_id'];

        $values = $this->_getHashValuesByEid($eid, $language, $version);
        $values['template_id'] = $templateId;

        return $values;
    }
}