<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\File;

use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Component\MetaSet\Domain\MetaSet;
use Phlexible\Component\MetaSet\Domain\MetaSetField;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;
use Phlexible\Component\MetaSet\Model\MetaSetManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * File meta set manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetManager implements MetaSetManagerInterface
{
    /**
     * @var MetaSetRepositoryInterface
     */
    private $repository;

    /**
     * @param MetaSetRepositoryInterface $repository
     */
    public function __construct(MetaSetRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->repository->load($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        $accessor = new PropertyAccessor();

        $metaSets = $this->findAll();

        if (count($criteria)) {
            $metaSets = array_filter($metaSets, function ($metaSet) use ($criteria, $accessor) {
                foreach ($criteria as $field => $value) {
                    if ($accessor->getValue($metaSet, $field) === $value) {
                        return true;
                    }
                }
            });
        }

        if ($orderBy) {
            usort($metaSets, function ($a, $b) use ($orderBy, $accessor) {
                foreach ($orderBy as $field => $dir) {
                    $result = $accessor->getValue($a, $field) > $accessor->getValue($b, $field);
                    if ($result !== 0) {
                        return $result;
                    }
                }

                return 0;
            });
        }

        if ($limit) {
            if (!$offset) {
                $offset = 0;
            }

            $metaSets = array_slice($metaSets, $offset, $limit);
        }

        return array_values($metaSets);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria = array(), array $orderBy = null)
    {
        $metaSets = $this->findBy($criteria, $orderBy, 1);

        if (!$metaSets) {
            return null;
        }

        return current($metaSets);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return array_values($this->repository->loadAll()->all());
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaSet()
    {
        return new MetaSet();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaSetField()
    {
        return new MetaSetField();
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetaSet(MetaSetInterface $metaSet)
    {
        if (!$metaSet->getId()) {
            $metaSet->setId(Uuid::generate());
        }

        $this->repository->writeMetaSet($metaSet, 'xml');
    }
}
