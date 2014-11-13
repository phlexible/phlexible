<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MetaSetBundle\Doctrine\MetaDataManager;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSet;
use Phlexible\Bundle\MetaSetBundle\Model\MetaData;
use Phlexible\Bundle\MetaSetBundle\Model\MetaDataInterface;

/**
 * Folder meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderMetaDataManager extends MetaDataManager
{
    /**
     * @param MetaSet         $metaSet
     * @param FolderInterface $folder
     *
     * @return MetaData|MetaDataInterface
     */
    public function createFolderMetaData(MetaSet $metaSet, FolderInterface $folder)
    {
        $metaData = $this->createMetaData($metaSet);
        $metaData
            ->setIdentifiers($this->getIdentifiersFromFolder($folder));

        return $metaData;
    }

    /**
     * @param MetaSet         $metaSet
     * @param FolderInterface $folder
     *
     * @return null|MetaDataInterface
     */
    public function findByMetaSetAndFolder(MetaSet $metaSet, FolderInterface $folder)
    {
        return $this->findByMetaSetAndIdentifiers($metaSet, $this->getIdentifiersFromFolder($folder));
    }

    /**
     * @param FolderInterface $folder
     *
     * @return array
     */
    private function getIdentifiersFromFolder(FolderInterface $folder)
    {
        return [
            'folder_id' => $folder->getId(),
        ];
    }
}
