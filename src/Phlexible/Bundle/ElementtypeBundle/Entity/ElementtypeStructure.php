<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Elementtype structure
 *
 * @author Phillip Look <plook@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="elementtype_structure")
 */
class ElementtypeStructure
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue("AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Elementtype
     * @ORM\ManyToOne(targetEntity="Elementtype")
     * @ORM\JoinColumn(name="elementtype_id", referencedColumnName="id")
     */
    private $elementtype;

    /**
     * @var int
     * @ORM\Column(name="elementtype_version", type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(name="ds_id", type="string", length=36, options={"fixed"=true})
     */
    private $dsId;

    /**
     * @var int
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string
     * @ORM\Column(name="parent_ds_id", type="string", length=36, nullable=true, options={"fixed"=true})
     */
    private $parentDsId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @var Elementtype
     * @ORM\ManyToOne(targetEntity="Elementtype")
     * @ORM\JoinColumn(name="reference_id", referencedColumnName="id", nullable=true)
     */
    private $referenceElementtype;

    /**
     * @var int
     * @ORM\Column(name="reference_version", type="integer", nullable=true)
     */
    private $referenceVersion;

    /**
     * @var string
     * @ORM\Column(type="string", length=20)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $configuration;

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $validation;

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $labels;

    /**
     * @var string
     * @ORM\Column(type="json_array", nullable=true)
     */
    private $options;

    /**
     * @var string
     * @ORM\Column(name="content_channels", type="json_array", nullable=true)
     */
    private $contentChannels;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;
}
