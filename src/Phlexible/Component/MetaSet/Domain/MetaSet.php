<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\Domain;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Component\MetaSet\Model\MetaSetFieldInterface;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * Meta set.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSet implements MetaSetInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $revision;

    /**
     * @var MetaSetFieldInterface[]|ArrayCollection
     */
    private $fields;

    /**
     * @var string
     */
    private $createdBy;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var string
     */
    private $modifiedBy;

    /**
     * @var DateTime
     */
    private $modifiedAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRevision()
    {
        return $this->revision;
    }

    /**
     * {@inheritdoc}
     */
    public function setRevision($revision)
    {
        $this->revision = $revision;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * {@inheritdoc}
     */
    public function addField(MetaSetFieldInterface $field)
    {
        $this->fields->set($field->getName(), $field);
        $field->setMetaSet($this);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasField($name)
    {
        return $this->fields->containsKey($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getField($name)
    {
        return $this->fields->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldById($id)
    {
        foreach ($this->fields as $field) {
            if ((string) $field->getId() === (string) $id) {
                return $field;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function removeField(MetaSetFieldInterface $field)
    {
        if ($this->fields->contains($field)) {
            $this->fields->removeElement($field);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedBy()
    {
        return $this->modifiedBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifiedBy($modifiedBy)
    {
        $this->modifiedBy = $modifiedBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModifiedAt()
    {
        return $this->modifiedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setModifiedAt($modifiedAt)
    {
        $this->modifiedAt = $modifiedAt;

        return $this;
    }
}
