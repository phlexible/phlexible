<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFolderInterface;
use Phlexible\Bundle\MetaSetBundle\Doctrine\MetaDataManager;
use Phlexible\Bundle\MetaSetBundle\Model\MetaData;
use Phlexible\Bundle\MetaSetBundle\Model\MetaDataInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSet;

/**
 * Folder meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderMetaDataManager extends MetaDataManager
{
    /**
     * @param MetaSet                 $metaSet
     * @param ExtendedFolderInterface $folder
     *
     * @return MetaData|MetaDataInterface
     */
    public function createFolderMetaData(MetaSet $metaSet, ExtendedFolderInterface $folder)
    {
        $metaData = $this->createMetaData($metaSet);
        $metaData
            ->setIdentifiers($this->getIdentifiersFromFolder($folder));

        return $metaData;
    }

    /**
     * @param MetaSet                 $metaSet
     * @param ExtendedFolderInterface $folder
     *
     * @return null|MetaDataInterface
     */
    public function findByMetaSetAndFolder(MetaSet $metaSet, ExtendedFolderInterface $folder)
    {
        return $this->findByMetaSetAndIdentifiers($metaSet, $this->getIdentifiersFromFolder($folder));
    }

    /**
     * @param ExtendedFolderInterface $folder
     *
     * @return array
     */
    private function getIdentifiersFromFolder(ExtendedFolderInterface $folder)
    {
        return [
            'folder_id' => $folder->getId(),
        ];
    }
}
