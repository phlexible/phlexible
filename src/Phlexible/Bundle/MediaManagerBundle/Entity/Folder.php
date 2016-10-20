<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Component\AccessControl\Model\HierarchicalDomainObjectInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFolderInterface;
use Phlexible\Component\Volume\Model\Folder as BaseFolder;

/**
 * Folder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="media_folder")
 */
class Folder extends BaseFolder implements ExtendedFolderInterface, HierarchicalDomainObjectInterface
{
    /**
     * @var array
     * @ORM\Column(name="metasets", type="simple_array", nullable=true)
     */
    private $metasets = [];

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function addMetaSet($metaSetId)
    {
        if ($metaSetId && !in_array($metaSetId, $this->metasets)) {
            $this->metasets[] = $metaSetId;
        }

        return $this;
    }

    /**
     * @param string $metaSetId
     *
     * @return $this
     */
    public function removeMetaSet($metaSetId)
    {
        if (in_array($metaSetId, $this->metasets)) {
            unset($this->metasets[array_search($metaSetId, $this->metasets)]);
        }

        return $this;
    }

    /**
     * @param array $metasets
     *
     * @return $this
     */
    public function setMetaSets(array $metasets)
    {
        $this->metasets = $metasets;

        return $this;
    }

    /**
     * @return array
     */
    public function getMetaSets()
    {
        return $this->metasets;
    }

    /**
     * Return domain object identifier
     *
     * @return array
     */
    public function getObjectIdentifier()
    {
        return $this->getId();
    }

    /**
     * Return domain object type
     *
     * @return array
     */
    public function getObjectType()
    {
        return get_class($this);
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchicalObjectIdentifiers()
    {
        return $this->getIdPath();
    }
}
