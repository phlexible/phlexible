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
 * Teaser online
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="teaser_online")
 */
class TeaserOnline
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
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var string
     * @ORM\Column(name="publish_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $publishUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="published_at", type="datetime")
     */
    private $publishedAt;
}
