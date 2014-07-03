<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle;

/**
 * Asset meta identifier
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaIdentifier implements \Media_MetaSets_Item_Interface
{
    /**
     * @var string
     */
    private $_fileId;

    /**
     * @var int
     */
    private $_fileVersion;

    private $_language = 'de';

    public function __construct($fileId, $fileVersion = 1, $language = 'de')
    {
        $this->_fileId = $fileId;
        $this->_fileVersion = $fileVersion;
        $this->_language = $language;
    }

    /**
     * @see Media_MetaSets_Item_Interface::getIdentifiers()
     *
     * @return array
     */
    public function getIdentifiers()
    {
        return array(
            'file_id'      => $this->_fileId,
            'file_version' => $this->_fileVersion,
        );
    }

    /**
     * @see Media_MetaSets_Item_Interface::getKeyField()
     *
     * @return string
     */
    public function getKeyField()
    {
        return 'meta_key';
    }

    /**
     * @see Media_MetaSets_Item_Interface::getTableName()
     *
     * @return string
     */
    public function getTableName()
    {
        return DB_PREFIX . 'mediamanager_files_metasets_items';
    }

    /**
     * @see Media_MetaSets_Item_Interface::getValueField()
     *
     * @return string
     */
    public function getValueField()
    {
        return 'meta_value_' . $this->_language;
    }
}