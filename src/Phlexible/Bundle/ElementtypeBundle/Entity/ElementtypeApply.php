<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Elementtype apply
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="elementtype_apply")
 */
class ElementtypeApply
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue("AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(name="elementtype_id", type="string")
     */
    private $elementtypeId;

    /**
     * @var string
     * @ORM\Column(name="under_elementtype_id", type="string")
     */
    private $underElementtypeId;

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
     * @return mixed
     */
    public function getElementtypeId()
    {
        return $this->elementtypeId;
    }

    /**
     * @param string $elementtype
     *
     * @return $this
     */
    public function setElementtypeId($elementtype)
    {
        $this->elementtypeId = $elementtype;

        return $this;
    }

    /**
     * @return string
     */
    public function getUnderElementtypeId()
    {
        return $this->underElementtypeId;
    }

    /**
     * @param string $underElementtype
     *
     * @return $this
     */
    public function setUnderElementtypeId($underElementtype)
    {
        $this->underElementtypeId = $underElementtype;

        return $this;
    }
}
