<?php
/**
 * Mediamanager
 *
 * PHP Version 5
 *
 * @category    Mediamanager
 * @package     Media_Site
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Exception.php 2608 2007-02-23 15:57:36Z swentz $
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

/**
 * Identifier for folder meta items.
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class FolderMetaIdentifier implements Media_MetaSets_Item_Interface
{
    /**
     * @var string
     */
    private $folderId;

    /**
      * @var string
      */
    private $language;

    /**
     * @param string $folderId
     * @param string $language
     */
    public function __construct($folderId, $language)
    {
        $this->folderId = $folderId;
        $this->language = $language;
    }

    /**
     * @see Media_MetaSets_Item_Interface::getIdentifiers()
     *
     * @return array
     */
    public function getIdentifiers()
    {
        return array(
            'folder_id' => $this->folderId,
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
        return DB_PREFIX . 'mediamanager_folder_metasets_items';
    }

    /**
     * @see Media_MetaSets_Item_Interface::getValueField()
     *
     * @return string
     */
    public function getValueField()
    {
        return 'meta_value_' . $this->language;
    }
}
