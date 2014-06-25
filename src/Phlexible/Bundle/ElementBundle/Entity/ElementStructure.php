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
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $version;

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
     * @var int
     * @ORM\Column(name="cnt", type="integer", nullable=true)
     */
    private $count;

    /**
     * @var bool
     * @ORM\Column(name="repeatable_node", type="boolean", nullable=true)
     */
    private $repeatableNode = false;

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
}