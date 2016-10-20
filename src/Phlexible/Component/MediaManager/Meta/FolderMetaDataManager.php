<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\Meta;

use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\MediaManagerBundle\Entity\FolderMetaDataValue;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Phlexible\Component\MetaSet\Doctrine\MetaDataManager;
use Phlexible\Component\MetaSet\Model\MetaData;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Model\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * Folder meta data manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderMetaDataManager extends MetaDataManager
{
    /**
     * {@inheritdoc}
     */
    public function createMetaData(MetaSet $metaSet)
    {
        return new MetaData($metaSet);
    }

    /**
     * @param MetaSetInterface        $metaSet
     * @param ExtendedFolderInterface $folder
     *
     * @return null|MetaDataInterface
     */
    public function findByMetaSetAndFolder(MetaSetInterface $metaSet, ExtendedFolderInterface $folder)
    {
        return $this->findOneByMetaSetAndTarget($metaSet, $folder);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataClass()
    {
        return FolderMetaDataValue::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function joinTarget(QueryBuilder $qb, $folder)
    {
        /* @var $folder ExtendedFolderInterface */
        $qb->join('d.folder', 'f');
        $qb->andWhere($qb->expr()->eq("f.id", ':folderId'));
        $qb->setParameter('folderId', $folder->getId());
    }

    /**
     * @param string                  $setId
     * @param string                  $language
     * @param string                  $fieldId
     * @param ExtendedFolderInterface $target
     *
     * @return FolderMetaDataValue
     */
    protected function getOrCreateMetaDataValue($setId, $language, $fieldId, $target)
    {
        $metaDataValue = $this->getDataRepository()->findOneBy(array(
            'setId' => $setId,
            'language' => $language,
            'fieldId' => $fieldId,
            'folder' => $target,
        ));

        if (!$metaDataValue) {
            $metaDataValue = new FolderMetaDataValue(
                $setId,
                $target,
                $language,
                $fieldId
            );
        }

        return $metaDataValue;
    }
}
