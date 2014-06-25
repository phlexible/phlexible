<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Manager for folder meta items.
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class FolderMeta
{
    /**
      * @var FolderInterface
      */
    private $folder;

    /**
      * @var Media_Site_Folder_Meta_Manager
      */
    private $_metaManager;

    /**
     * @param FolderInterface $folder
     */
    public function __construct(FolderInterface $folder)
    {
        $this->folder = $folder;

        $container          = MWF_Registry::getContainer();
        $this->_metaManager = $container->mediaSiteFolderMetaManager;
    }

    /**
     * @param string $language
     *
     * @return array
     */
    public function getMeta($language)
    {
        return $this->_metaManager->getMeta($this->folder->getID(), $language);
    }

    /**
     * @param $language
     *
     * @return array
     */
    public function getMetaSetItems($language)
    {
        return $this->_metaManager->getMetaSetItems($this->folder->getID(), $language);
    }

}
