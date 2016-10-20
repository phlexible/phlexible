<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Element structure
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_structure", uniqueConstraints={@ORM\UniqueConstraint(columns={"data_id", "element_version_id"})})
 */
class ElementStructure
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @var ElementStructure
     * @ORM\ManyToOne(targetEntity="ElementStructure")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $parentStructure;

    /**
     * @var string
     * @ORM\Column(name="data_id", type="string", length=36, options={"fixed"=true})
     */
    private $dataId;

    /**
     * @var ElementVersion
     * @ORM\ManyToOne(targetEntity="ElementVersion")
     * @ORM\JoinColumn(name="element_version_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $elementVersion;

    /**
     * @var string
     * @ORM\Column(name="ds_id", type="string", length=36, options={"fixed"=true})
     */
    private $dsId;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="parent_ds_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private $parentDsId;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sort;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return ElementStructure
     */
    public function getParentStructure()
    {
        return $this->parentStructure;
    }

    /**
     * @param ElementStructure $parentStructure
     *
     * @return $this
     */
    public function setParentStructure(ElementStructure $parentStructure = null)
    {
        $this->parentStructure = $parentStructure;

        return $this;
    }

    /**
     * @return string
     */
    public function getDataId()
    {
        return $this->dataId;
    }

    /**
     * @param string $dataId
     *
     * @return $this
     */
    public function setDataId($dataId)
    {
        $this->dataId = $dataId;

        return $this;
    }

    /**
     * @return ElementVersion
     */
    public function getElementVersion()
    {
        return $this->elementVersion;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return $this
     */
    public function setElementVersion(ElementVersion $elementVersion)
    {
        $this->elementVersion = $elementVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getDsId()
    {
        return $this->dsId;
    }

    /**
     * @param string $dsId
     *
     * @return $this
     */
    public function setDsId($dsId)
    {
        $this->dsId = $dsId;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getParentDsId()
    {
        return $this->parentDsId;
    }

    /**
     * @param string $parentDsId
     *
     * @return $this
     */
    public function setParentDsId($parentDsId)
    {
        $this->parentDsId = $parentDsId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     *
     * @return $this
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }
}
