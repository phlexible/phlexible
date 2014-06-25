<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Field;

use Phlexible\Bundle\ElementtypeBundle\Field\AbstractField as BaseAbstractField;

/**
 * Abstract media field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AbstractField extends BaseAbstractField
{
    const TYPE = 'image';
    public $options = false;
    public $icon = 'm-frontendmediamanager-field_image-icon';

    /**
     * Transform item values
     *
     * @param array $content
     *
     * @return Media_Site_File_Abstract
     */
    protected function _getFile($content)
    {
        try
        {
            $split       = explode(';', $content);
            $fileId      = $split[0];
            $fileVersion = !empty($split[1]) ? $split[1] : 1;

            $siteManager = Media_Site_Manager::getInstance();
            $filePeer    = $siteManager->getByFileId($fileId)->getFilePeer();

            $file        = $filePeer->getByID($fileId, $fileVersion);
        }
        catch (Exception $e)
        {
            MWF_Log::exception($e);
            $file = null;
        }

        return $file;
    }

    protected function _getMasterData(array $item)
    {
        $master = array();

        try
        {
            if (!empty($item['data_options']['unlinked']) && !empty($item['data_options']['master_value']))
            {
                $split       = explode(';', $item['data_options']['master_value']);
                $fileId      = $split[0];
                $fileVersion = !empty($split[1]) ? $split[1] : 1;

                $file = $this->_getFile($item['data_options']['master_value']);

                if ($file !== null)
                {
                    $master['file_id']      = $file->getID();
                    $master['file_version'] = $file->getVersion();
                    $master['folder_id']    = $file->getFolderID();
                    $master['folder_path']  = $file->getFolder()->getIdPath();
                    $master['name']         = $file->getName();
                }
            }
        }
        catch (Exception $e)
        {
            MWF_Log::exception($e);
            $master = array();
        }

        return $master;
    }

    protected function _getTemplateConfig($media, Media_Site_File_Abstract $file)
    {
        $templateConfig = array();

        if (empty($media['imageList']))
        {
            return $templateConfig;
        }

        if (!empty($media['imageList']))
        {
            foreach ($media['imageList'] as $imageTemplateRow)
            {
                $imageTemplateKey = $imageTemplateRow[0];
                $imageTemplate = MWF_Registry::getContainer()->get('mediatemplates.repository')->find($imageTemplateKey);

                $templateConfig[$imageTemplateKey] = array(
                    'type'   => 'image',
                    'width'  => $imageTemplate->getWidth(),
                    'height' => $imageTemplate->getHeight(),
                    'method' => $imageTemplate->getMethod(),
                );
            }
        }

        if (!empty($media['videoList']))
        {
            foreach ($media['videoList'] as $videoTemplateRow)
            {
                $videoTemplateKey = $videoTemplateRow[0];
                $videoTemplate = MWF_Registry::getContainer()->get('mediatemplates.repository')->find($videoTemplateKey);

                $templateConfig[$videoTemplateKey] = array(
                    'type'   => 'video',
                    'width'  => $videoTemplate->getWidth(),
                    'height' => $videoTemplate->getHeight(),
                );
            }
        }

        if (!empty($media['audioList']))
        {
            foreach ($media['audioList'] as $audioTemplateRow)
            {
                $audioTemplateKey = $audioTemplateRow[0];
                $audioTemplate = MWF_Registry::getContainer()->get('mediatemplates.repository')->find($audioTemplateKey);

                $templateConfig[$audioTemplateKey] = array(
                    'type'   => 'audio',
                );
            }
        }

        return $templateConfig;
    }

    /**
     * Return media data
     *
     * @param Media_Site_File_Abstract $file
     *
     * @return array
     */
    protected function _getMediaData(Media_Site_File_Abstract $file)
    {
        $media = array();

        try
        {
            $attributes   = $file->getAsset()->getAttributes();
            $documentType = $file->getDocumentType();

            $mediaTemp['file_id']      = $file->getID();
            $mediaTemp['file_version'] = $file->getVersion();
            $mediaTemp['folder_id']    = $file->getFolderID();
            $mediaTemp['folder_path']  = $file->getFolder()->getIdPath();
            $mediaTemp['name']         = $file->getName();
            $mediaTemp['size']         = $file->getSize();
            $mediaTemp['readablesize'] = Brainbits_Format_Filesize::format($file->getSize());
            $mediaTemp['mimetype']     = $file->getMimeType();
            $mediaTemp['documenttype'] = $documentType->getKey();
            $mediaTemp['assettype']    = $file->getAssetType();

            try
            {
                $meta = $file->getAsset()->getMeta($this->_language);
                if ($meta)
                {
                    foreach ($meta as $metaKey => $metaRow)
                    {
                        $mediaTemp['meta'][$metaKey] = $metaRow['value'];
                    }
                }
            }
            catch (Exception $e)
            {
                MWF_Log::exception($e);
                $mediaTemp['meta'] = array();
            }

            $container        = MWF_Registry::getContainer();
            $mediaSiteManager = $container->get('mediasite.manager')->getByFileId($file->getID());
            $storageDriver    = $mediaSiteManager->getStorageDriver();

            $urls             = $storageDriver->getUrls($file);
            $media            = array_merge($mediaTemp, $urls);
        }
        catch (Exception $e)
        {
            MWF_Log::exception($e);
            $media = array();
        }

        return $media;
    }

    /**
     * Get image templates
     *
     * @param array $item
     * @param array $media
     * @param Media_Site_File_Abstract $file
     *
     * @return array
     */
    protected function _getImageTemplates(array $item, $media, Media_Site_File_Abstract $file)
    {
        if (empty($media['imageList']))
        {
            return array();
        }

        if ($file !== null)
        {
            $container        = MWF_Registry::getContainer();
            $mediaSiteManager = $container->get('mediasite.manager')->getByFileId($file->getID());
            $storageDriver    = $mediaSiteManager->getStorageDriver();
        }

        $manager   = Media_Cache_Manager::getInstance();
        $templates = array();

        foreach ($media['imageList'] as $imageTemplateRow)
        {
            $imageTemplateKey = $imageTemplateRow[0];

            if (!$imageTemplateKey && $imageTemplateKey !== 0)
            {
                continue;
            }

            $image = array();

            if ($file !== null)
            {
                $cacheItem     = $manager->getByTemplateAndFile($imageTemplateKey, $file->getId(), $file->getVersion());
                $cacheFileName = $this->_replaceExtension($file->getName(), $cacheItem->getExtension());

                $imageTemp     = array(
                    'cache_id'     => $cacheItem->getId(),
                    'name'         => rawurlencode($cacheFileName), // rawurlencode is historic, don't know if necessary
                    'width'        => $cacheItem->getWidth(),
                    'height'       => $cacheItem->getHeight(),
                    'size'         => $cacheItem->getFileSize(),
                    'readablesize' => Brainbits_Format_Filesize::format($cacheItem->getFileSize()),
                    'mimetype'     => $cacheItem->getMimeType(),
                );

                $urls  = $storageDriver->getCacheUrls($file, $cacheItem);
                $image = array_merge($imageTemp, $urls);
            }

            $imageTemplate   = MWF_Registry::getContainer()->get('mediatemplates.repository')->find($imageTemplateKey);
            $image['config'] = array(
                'type'   => 'image',
                'width'  => $imageTemplate->getWidth(),
                'height' => $imageTemplate->getHeight(),
                'method' => $imageTemplate->getMethod(),
            );

            $templates[$imageTemplateKey] = $image;
        }

        return $templates;
    }

    /**
     * Get video templates
     *
     * @param array $item
     * @param array $media
     * @param Media_Site_File_Abstract $file
     *
     * @return array
     */
    protected function _getVideoTemplates($item, $media, Media_Site_File_Abstract $file)
    {
        if (empty($media['videoList']))
        {
            return array();
        }

        if (empty($item['media']['assettype']) || $item['media']['assettype'] !== 'VIDEO')
        {
            return array();
        }

        if ($file !== null)
        {
            $container        = MWF_Registry::getContainer();
            $mediaSiteManager = $container->get('mediasite.manager')->getByFileId($file->getID());
            $storageDriver    = $mediaSiteManager->getStorageDriver();
        }

        $manager   = Media_Cache_Manager::getInstance();
        $templates = array();

        foreach ($media['videoList'] as $videoTemplateRow)
        {
            $videoTemplateKey = $videoTemplateRow[0];

            if (!$videoTemplateKey && $videoTemplateKey !== 0)
            {
                continue;
            }

            $video = array();

            if ($file !== null)
            {

                $cacheItem     = $manager->getByTemplateAndFile($videoTemplateKey, $file->getId(), $file->getVersion());
                $cacheFileName = $this->_replaceExtension($file->getName(), $cacheItem->getExtension());

                $videoTemp = array(
                    'cache_id'     => $cacheItem->getId(),
                    'name'         => rawurlencode($cacheFileName), // rawurlencode is historic, don't know if necessary
                    'width'        => $cacheItem->getWidth(),
                    'height'       => $cacheItem->getHeight(),
                    'size'         => $cacheItem->getFileSize(),
                    'readablesize' => Brainbits_Format_Filesize::format($cacheItem->getFileSize()),
                    'mimetype'     => $cacheItem->getMimeType(),
                );
            }

            $urls  = $storageDriver->getCacheUrls($file, $cacheItem);
            $video = array_merge($videoTemp, $urls);

            $templates[$videoTemplateKey] = $video;
        }

        return $templates;
    }

    /**
     * Get audio templates
     *
     * @param array $item
     * @param array $media
     * @param Media_Site_File_Abstract $file
     *
     * @return array
     */
    protected function _getAudioTemplates($item, $media, Media_Site_File_Abstract $file)
    {
        if (empty($media['audioList']))
        {
            return array();
        }

        if (empty($item['media']['assettype']) || $item['media']['assettype'] !== 'AUDIO')
        {
            return array();
        }

        if ($file !== null)
        {
            $container        = MWF_Registry::getContainer();
            $mediaSiteManager = $container->get('mediasite.manager')->getByFileId($file->getID());
            $storageDriver    = $mediaSiteManager->getStorageDriver();
        }

        $manager = Media_Cache_Manager::getInstance();
        $templates = array();

        foreach ($media['audioList'] as $audioTemplateRow)
        {
            $audioTemplateKey = $audioTemplateRow[0];

            if (!$audioTemplateKey && $audioTemplateKey !== 0)
            {
                continue;
            }

            $audio = array();

            if ($file !== null)
            {
                $cacheItem     = $manager->getByTemplateAndFile($audioTemplateKey, $file->getId(), $file->getVersion());
                $cacheFileName = $this->_replaceExtension($file->getName(), $cacheItem->getExtension());

                $audioTemp = array(
                    'type'         => 'audio',
                    'cache_id'     => $cacheItem->getId(),
                    'name'         => rawurlencode($cacheFileName), // rawurlencode is historic, don't know if necessary
                    'size'         => $cacheItem->getFileSize(),
                    'readablesize' => Brainbits_Format_Filesize::format($cacheItem->getFileSize()),
                    'mimetype'     => $cacheItem->getMimeType(),
                );
            }

            $urls  = $storageDriver->getCacheUrls($file, $cacheItem);
            $audio = array_merge($audioTemp, $urls);

            $templates[$audioTemplateKey] = $audio;
        }

        return $templates;
    }

    /**
     * Remove a filename extension
     *
     * @param string $filename
     *
     * @return string
     */
    protected function _removeExtension($filename)
    {
        if (strrpos($filename, '.') !== false)
        {
            $filename = substr($filename, 0, strrpos($filename, '.'));
        }

        return $filename;
    }

    /**
     * Remove a filename extension
     *
     * @param string $filename
     * @param string $ext
     *
     * @return string
     */
    protected function _replaceExtension($filename, $ext)
    {
        $filename = $this->_removeExtension($filename);

        return $filename . '.' . $ext;
    }
}