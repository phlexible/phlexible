<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Field;

/**
 * Folder field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderField extends AbstractField
{
    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return 'p-frontendmedia-field_folder-icon';
    }

    /**
     * Transform item values
     *
     * @param array $item
     * @param array $media
     * @param array $options
     *
     * @return array
     */
    protected function _transform(array $item, array $media, array $options)
    {
        $item['templates'] = array();
        $item['templates_config'] = array();
        $item['media'] = array();

        try {
            if (!empty($item['data_content'])) {
                $folder = $this->_getFolder($item['data_content']);

                if ($folder !== null) {
                    $item['media'] = $this->_getFolderMediaData($folder);
                    // $item['master'] = $this->_getMasterData($item);
                }
            }

        } catch (Exception $e) {
            MWF_Log::exception($e);
            $item['data_content'] = '';
            $item['media'] = false;
        }

        return $item;
    }

    /**
     * Transform item values
     *
     * @param array $item
     * @param array $media
     * @param array $options
     *
     * @return Media_Site_Folder_Abstract
     */
    protected function _getFolder($folderId)
    {
        try {
            $siteManager = Media_Site_Manager::getInstance();
            $folderPeer = $siteManager->getByFolderId($folderId)->getFolderPeer();

            $folder = $folderPeer->getByID($folderId);
        } catch (Exception $e) {
            MWF_Log::exception($e);
            $folder = null;
        }

        return $folder;
    }

    protected function _getFolderMediaData(Media_Site_Folder_Abstract $folder)
    {
        $media = array();

        try {
            $media['folder_id'] = $folder->getID();
            $media['folder_id_path'] = $folder->getIdPath();
            $media['name'] = $folder->getName();
            $media['folder_path'] = $folder->getPath();
        } catch (Exception $e) {
            MWF_Log::exception($e);
            $media = array();
        }

        return $media;
    }
}
