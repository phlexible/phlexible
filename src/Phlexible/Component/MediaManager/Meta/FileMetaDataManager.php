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
use Phlexible\Bundle\MediaManagerBundle\Entity\FileMetaDataValue;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MetaSet\Doctrine\MetaDataManager;
use Phlexible\Component\MetaSet\Domain\MetaData;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Domain\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * File meta data manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileMetaDataManager extends MetaDataManager
{
    /**
     * {@inheritdoc}
     */
    public function createMetaData(MetaSet $metaSet)
    {
        return new MetaData($metaSet);
    }

    /**
     * @param MetaSetInterface      $metaSet
     * @param ExtendedFileInterface $file
     *
     * @return MetaDataInterface|null
     */
    public function findByMetaSetAndFile(MetaSetInterface $metaSet, ExtendedFileInterface $file)
    {
        return $this->findOneByMetaSetAndTarget($metaSet, $file);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataClass()
    {
        return FileMetaDataValue::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function joinTarget(QueryBuilder $qb, $file)
    {
        /* @var $file ExtendedFileInterface */
        $qb->join('d.file', 'f');
        $qb->andWhere($qb->expr()->eq('f.id', ':fileId'));
        $qb->setParameter('fileId', $file->getId());
        $qb->andWhere($qb->expr()->eq('f.version', ':fileVersion'));
        $qb->setParameter('fileVersion', $file->getVersion());
    }

    /**
     * @param string                $setId
     * @param string                $language
     * @param string                $fieldId
     * @param ExtendedFileInterface $target
     *
     * @return FileMetaDataValue
     */
    protected function getOrCreateMetaDataValue($setId, $language, $fieldId, $target)
    {
        $metaDataValue = $this->getDataRepository()->findOneBy(array(
            'setId' => $setId,
            'language' => $language,
            'fieldId' => $fieldId,
            'file' => $target,
        ));

        if (!$metaDataValue) {
            $metaDataValue = new FileMetaDataValue(
                $setId,
                $target,
                $language,
                $fieldId
            );
        }

        return $metaDataValue;
    }
}
