<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Meta;

use Doctrine\ORM\QueryBuilder;
use Phlexible\Bundle\ElementBundle\Entity\ElementMetaDataValue;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Component\MetaSet\Doctrine\MetaDataManager;
use Phlexible\Component\MetaSet\Domain\MetaData;
use Phlexible\Component\MetaSet\Model\MetaDataInterface;
use Phlexible\Component\MetaSet\Domain\MetaSet;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * Element meta data manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementMetaDataManager extends MetaDataManager
{
    /**
     * {@inheritdoc}
     */
    public function createMetaData(MetaSet $metaSet)
    {
        return new MetaData($metaSet);
    }

    /**
     * @param MetaSetInterface $metaSet
     * @param ElementVersion   $elementVersion
     *
     * @return MetaDataInterface|null
     */
    public function findByMetaSetAndElementVersion(MetaSetInterface $metaSet, ElementVersion $elementVersion)
    {
        return $this->findOneByMetaSetAndTarget($metaSet, $elementVersion);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDataClass()
    {
        return ElementMetaDataValue::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function joinTarget(QueryBuilder $qb, $elementVersion)
    {
        /* @var $elementVersion ElementVersion */
        $qb->join('d.elementVersion', 'e');
        $qb->andWhere($qb->expr()->eq('e.id', ':elementVersionId'));
        $qb->setParameter('elementVersionId', $elementVersion->getId());
    }

    /**
     * @param string         $setId
     * @param string         $language
     * @param string         $fieldId
     * @param ElementVersion $target
     *
     * @return ElementMetaDataValue
     */
    protected function getOrCreateMetaDataValue($setId, $language, $fieldId, $target)
    {
        $metaDataValue = $this->getDataRepository()->findOneBy(array(
            'setId' => $setId,
            'language' => $language,
            'fieldId' => $fieldId,
            'elementVersion' => $target,
        ));

        if (!$metaDataValue) {
            $metaDataValue = new ElementMetaDataValue(
                $setId,
                $target,
                $language,
                $fieldId
            );
        }

        return $metaDataValue;
    }
}
