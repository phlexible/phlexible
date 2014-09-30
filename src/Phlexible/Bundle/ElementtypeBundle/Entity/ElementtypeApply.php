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
     * @var Elementtype
     * @ORM\ManyToOne(targetEntity="Elementtype")
     * @ORM\JoinColumn(name="elementtype_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $elementtype;

    /**
     * @var Elementtype
     * @ORM\ManyToOne(targetEntity="Elementtype")
     * @ORM\JoinColumn(name="apply_under_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $underElementtype;
}
