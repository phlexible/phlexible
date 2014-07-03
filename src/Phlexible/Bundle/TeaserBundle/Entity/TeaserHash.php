<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Teaser hash
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="teaser_hash")
 */
class TeaserHash
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var Teaser
     * @ORM\ManyToOne(targetEntity="Teaser")
     * @ORM\JoinColumn(name="teaser_id", referencedColumnName="id")
     */
    private $teaser;

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
     * @ORM\Column(type="string", length=32, options={"fixed"=true})
     */
    private $hash = false;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $debug;
}
