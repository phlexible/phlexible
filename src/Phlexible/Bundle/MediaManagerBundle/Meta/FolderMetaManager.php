<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Component\Database\ConnectionManager;

/**
 * Manager for folder meta items.
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class FolderMetaManager
{
    /** @var string */
    const META_SET_CLASSNAME = 'Media_MetaSets_Item';

    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var string
     */
    private $defaultLanguage;

    /**
     * @param ConnectionManager $connectionManager
     * @param string            $defaultLanguage
     */
    public function __construct(ConnectionManager $connectionManager, $defaultLanguage)
    {
        $this->db              = $connectionManager->default;
        $this->defaultLanguage = $defaultLanguage;
    }

    private function getDefaultMetaSet()
    {
        $metaSet = $this->getContainer()->get('metasets.repository')->find('folder');

        return $metaSet;
    }

    public function getMeta($folderId, $language)
    {
        $metaSetItems = $this->getMetaSetItems($folderId, $language);

        $meta = array();
        foreach ($metaSetItems as $metaSetItemKey => $metaSetItem)
        {
            /* @var $metaSetItem Media_MetaSets_Item */
            foreach ($metaSetItem->toArray($language) as $metaKey => $metaRow)
            {
                $meta[$metaKey]          = $metaRow;
                $meta[$metaKey]['setId'] = $metaSetItemKey;
            }
        }

        // apply value from default language as fallback if value is not set
        if ($this->defaultLanguage !== $language)
        {
            $fallback = $this->getMeta($folderId, $this->defaultLanguage);

            foreach (array_keys($meta) as $metaKey)
            {
                if (!strlen($meta[$metaKey]['value'])
                    && isset($fallback[$metaKey]['value'])
                    && strlen($fallback[$metaKey]['value'])
                )
                {
                    $meta[$metaKey]['value'] = $fallback[$metaKey]['value'];
                }
            }
        }

        return $meta;
    }

    /**
     * Return meta sets
     *
     * @param string $folderId
     * @param string $language
     *
     * @return array
     */
    public function getMetaSetItems($folderId, $language = null)
    {
        if (null === $language) {
            $language = $this->defaultLanguage;
        }

        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'mediamanager_folder_metasets', 'set_id')
            ->where('folder_id = ?', $folderId);

        $existingSetIds = $this->db->fetchCol($select);

        if (!count($existingSetIds)) {
            $this->addDefaultMetaSet($folderId);

            $existingSetIds = $this->db->fetchCol($select);

            if (!count($existingSetIds)) {
                die("Empty set ids");
            }
        }

        $sets = array();
        foreach ($existingSetIds as $setId)
        {
            $sets[$setId] = $this->getMetaSetItem($folderId, $setId, $language);
        }

        return $sets;
    }

    /**
     * Add default meta set
     *
     * @param string $folderId
     * @param Media_MetaSets_Set $set
     *
     * @return Media_MetaSets_Item
     */
    public function addMetaSet($folderId, Media_MetaSets_Set $set)
    {
        $db = $this->_dbPool->write;

        $select = $db->select()
            ->from($db->prefix . 'mediamanager_folder_metasets', 'set_id')
            ->where('set_id = ?', $set->id)
            ->where('folder_id = ?', $folderId);

        $isExisting = (boolean) $db->fetchOne($select);

        if (!$isExisting)
        {
            $insertData = array(
                'set_id'    => $set->id,
                'folder_id' => $folderId,
            );

            $db->insert($db->prefix . 'mediamanager_folder_metasets', $insertData);
        }

        $setItem = $this->getMetaSetItem($folderId, $set->id, $this->defaultLanguage);

        return $setItem;
    }

    /**
     * Return meta set item
     *
     * @param string $folderId
     * @param string $setId
     * @param string $language
     *
     * @return Media_MetaSets_Item
     */
    private function getMetaSetItem($folderId, $setId, $language)
    {
        $identifier = $this->getMetaSetIdentifier($folderId, $language);

        return Media_MetaSets_Item_Peer::get($setId, $identifier, self::META_SET_CLASSNAME);
    }

    /**
     * Add default meta set
     *
     * @param string $folderId
     *
     * @return Media_MetaSets_Item
     */
    public function addDefaultMetaSet($folderId)
    {
        $defaultMetaSet = $this->getDefaultMetaSet();

        return $this->addMetaSet($folderId, $defaultMetaSet);
    }

    /**
     * Remove meta set
     *
     * @param string $folderId
     * @param Media_MetaSets_Set $set
     */
    public function removeMetaSet($folderId, Media_MetaSets_Set $set)
    {
        $db = $this->_dbPool->write;

        $db->delete(
            $db->prefix . 'mediamanager_folder_metasets',
            array(
                'set_id = ?'    => $set->id,
                'folder_id = ?' => $folderId,
            )
        );
    }

    /**
     * Return the meta set identifier for this asset
     *
     * @param string $folderId
     * @param string $language
     *
     * @return Media_Site_Folder_Meta_Identifier
     */
    private function getMetaSetIdentifier($folderId, $language)
    {
        return new Media_Site_Folder_Meta_Identifier($folderId, $language);
    }

}
