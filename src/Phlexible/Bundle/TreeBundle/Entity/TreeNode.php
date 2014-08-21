<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tree node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="tree")
 */
class TreeNode
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
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;

    /**
     * @var string
     * @ORM\Column(name="siteroot_id", type="string", length=36, options={"fixed"=true})
     */
    private $siterootId;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @var int
     * @ORM\Column(name="type_id", type="integer", nullable=true)
     */
    private $typeId;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $sort;

    /**
     * @var string
     * @ORM\Column(name="sort_mode", type="string", length=255)
     */
    private $sortMode;

    /**
     * @var string
     * @ORM\Column(name="sort_dir", type="string", length=255)
     */
    private $sortDir;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $attributes;

    /**
     * @var bool
     * @ORM\Column(name="in_navigation", type="boolean", options={"default"=0})
     */
    private $inNavigation;

    /**
     * @var string
     * @ORM\Column(name="create_user_id", type="string", length=36, options={"fixed"=true})
     */
    private $createUserId;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;
}