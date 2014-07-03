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
 * Element version mapped field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_version_mapped_field")
 */
class ElementVersionMappedField
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Element
     * @ORM\ManyToOne(targetEntity="Element")
     * @ORM\JoinColumn(name="eid", referencedColumnName="eid")
     */
    private $element;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $backend;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $page;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $navigation;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $date;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $forward;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom1;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom2;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom3;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom4;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $custom5;

}