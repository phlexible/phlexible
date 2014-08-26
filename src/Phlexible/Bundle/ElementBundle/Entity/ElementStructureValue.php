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
 * Element structure data
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_structure_value")
 */
class ElementStructureValue
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="data_id", type="integer")
     */
    private $dataId;

    /**
     * @var Element
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(name="eid", referencedColumnName="eid")
     */
    private $element;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $language;

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
     * @ORM\Column(name="repeatable_id", type="string", length=255)
     */
    private $repeatableId;

    /**
     * @var string
     * @ORM\Column(name="repeatable_ds_id", type="string", length=36, options={"fixed"=true})
     */
    private $repeatableDsId;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @var string
     * @ORM\Column(type="json_array", length=255, nullable=true)
     */
    private $options;
}