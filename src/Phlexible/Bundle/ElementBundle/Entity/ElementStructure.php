<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Element structure
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_structure")
 */
class ElementStructure
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="data_id", type="string", length=36, options={"fixed"=true})
     */
    private $dataId;

    /**
     * @var ElementVersion
     * @ORM\Id
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
     * @ORM\Column(name="repeatable_id", type="string", length=255, nullable=true)
     */
    private $repeatableId;

    /**
     * @var string
     * @ORM\Column(name="repeatable_ds_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private $repeatableDsId;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sort;

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
    public function getRepeatableId()
    {
        return $this->repeatableId;
    }

    /**
     * @param string $repeatableId
     *
     * @return $this
     */
    public function setRepeatableId($repeatableId)
    {
        $this->repeatableId = $repeatableId;

        return $this;
    }

    /**
     * @return string
     */
    public function getRepeatableDsId()
    {
        return $this->repeatableDsId;
    }

    /**
     * @param string $repeatableDsId
     *
     * @return $this
     */
    public function setRepeatableDsId($repeatableDsId)
    {
        $this->repeatableDsId = $repeatableDsId;

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