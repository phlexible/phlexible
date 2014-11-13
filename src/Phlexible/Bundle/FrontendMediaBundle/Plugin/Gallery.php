<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Plugin;

/**
 * Dwoo gallery plugin
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class Gallery extends \Dwoo\Plugin
{
    /**
     * ID of media folder
     *
     * @var int
     */
    protected $_folderId;

    /**
     * Path to partial for rendering gallery
     *
     * @var string
     */
    protected $_partial;

    /**
     * Limit of images per page
     *
     * @var int
     */
    protected $_limit;

    /**
     * Executes the helper
     *
     * @param int    $folder  ID of media folder
     * @param string $partial Path to partial
     * @param int    $limit   Limit of images per page
     */
    public function process($folder, $partial, $limit = 49, $templates = array('_mm_small', '_mm_medium'))
    {
        /* @var $request Makeweb_Frontend_Request */
        $request = MWF_Registry::get('request');
        $paginator = new Paginator($this->getPaginatorAdapter($folder));
        $paginator->setItemCountPerPage($limit)
            ->setCurrentPageNumber($request->getParam('page', 1));

        $partialAssigns = array(
            'paginator' => $paginator,
            'images'    => $this->_transformFilesToNodes($paginator->getCurrentItems(), $templates),
            'tid'       => $request->getTid(),
            'language'  => $request->getLanguage(),
        );

        if (!function_exists('Dwoo_Plugin_include')) {
            $this->dwoo->getLoader()->loadPlugin('include');
        }

        return Dwoo_Plugin_include($this->dwoo, $partial, null, null, null, $partialAssigns);
    }

    /**
     * Returns the Media Folder Paginator Adapter
     *
     * @param string $folderId ID of mediafolder
     *
     * @return Makeweb_Frontendmediamanager_Folder_PaginatorAdapter
     */
    public function getPaginatorAdapter($folderId)
    {
        $siteManager = Media_Site_Manager::getInstance();
        $site = $siteManager->getByFolderId($folderId);
        $folderPeer = $site->getFolderPeer();
        $folder = $folderPeer->getByID($folderId);

        return new Makeweb_Frontendmediamanager_Folder_PaginatorAdapter($folder);
    }

    /**
     * Converts an array with files to an array with element nodes
     *
     * @param array $files Array with files retrieved by folder peer
     * @param       array  with templates $templates
     *
     * @return array Array with element nodes
     */
    public function _transformFilesToNodes($files, $templates = array())
    {
        $field = new Makeweb_Frontendmediamanager_Field_Image();
        $nodes = array();

        foreach ($templates as $key => $template) {
            if (!is_array($template)) {
                $templates[$key] = array($template);
            }
        }

        /* @var $file Media_SiteDb_File */
        foreach ($files as $fileId => $file) {
            if ($file->getAssetType() == 'IMAGE') {
                $item = array(
                    'data_content' => $fileId,
                    'media'        => array('imageList' => $templates)
                );
                $nodes[] = $field->transform($item, 1, 2, 'de');
            }
        }

        return $nodes;
    }
}
