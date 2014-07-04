<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Meta;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MetaSetBundle\Doctrine\MetaDataManager;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSet;
use Phlexible\Bundle\MetaSetBundle\Model\MetaDataInterface;

/**
 * File meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileMetaDataManager extends MetaDataManager
{
    /**
     * @param MetaSet       $metaSet
     * @param FileInterface $file
     *
     * @return null|MetaDataInterface
     */
    public function findByMetaSetAndFile(MetaSet $metaSet, FileInterface $file)
    {
        return $this->findByMetaSetAndIdentifiers($metaSet, $this->getIdentifiersFromFile($file));
    }

    /**
     * @param FileInterface $file
     *
     * @return array
     */
    private function getIdentifiersFromFile(FileInterface $file)
    {
        return array(
            'file_id'      => $file->getId(),
            'file_version' => $file->getVersion(),
        );
    }
}
