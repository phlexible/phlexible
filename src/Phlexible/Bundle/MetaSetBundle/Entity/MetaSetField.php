<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Meta set field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="meta_set_field")
 */
class MetaSetField
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $options;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $synchronized = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $readonly = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $required = false;

    /**
     * @var MetaSet
     * @ORM\ManyToOne(targetEntity="MetaSet", inversedBy="fields")
     * @ORM\JoinColumn(name="metaset_id", referencedColumnName="id")
     */
    private $metaSet;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

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
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return $this
     */
    public function setRequired($required = true)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return array
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * @param bool $readonly
     *
     * @return $this
     */
    public function setReadonly($readonly = true)
    {
        $this->readonly = $readonly;

        return $this;
    }

    /**
     * @return array
     */
    public function isSynchronized()
    {
        return $this->synchronized;
    }

    /**
     * @param bool $synchronized
     *
     * @return $this
     */
    public function setSynchronized($synchronized = true)
    {
        $this->synchronized = $synchronized;

        return $this;
    }

    /**
     * @return MetaSet
     */
    public function getMetaSet()
    {
        return $this->metaSet;
    }

    /**
     * @param MetaSet $metaset
     *
     * @return $this
     */
    public function setMetaSet($metaset)
    {
        $this->metaset = $metaset;

        return $this;
    }
}