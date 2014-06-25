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
 * Element catch lookup element
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="catch_lookup_element")
 */
class ElementCatchLookupElement
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $eid;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var int
     * @ORM\Column(name="tree_id", type="integer")
     */
    private $treeId;

    /**
     * @var int
     * @ORM\Column(name="elementtype_id", type="integer")
     */
    private $elementtypeId;

    /**
     * @var bool
     * @ORM\Column(name="is_preview", type="boolean")
     */
    private $isPreview;

    /**
     * @var bool
     * @ORM\Column(name="in_navigation", type="boolean")
     */
    private $inNavigation;

    /**
     * @var bool
     * @ORM\Column(name="is_restricted", type="boolean")
     */
    private $isRestricted;

    /**
     * @var string
     * @ORM\Column(type="string", length=2, options={"fixed"=true})
     */
    private $language;

    /**
     * @var int
     * @ORM\Column(name="online_version", type="integer", nullable=true)
     */
    private $onlineVersion;

    /**
     * @var \DateTime
     * @ORM\Column(name="published_at", type="datetime")
     */
    private $publishedAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="custom_date", type="datetime", nullable=true)
     */
    private $customDate;

    /**
     * @var \DateTime
     * @ORM\Column(name="cached_at", type="datetime")
     */
    private $cachedAt;
}
