<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Meta set field
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="meta_set_field")
 */
class MetaSetField
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=36, options={"fixed"=true})
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $field;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $options;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $synchronized = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $readonly = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $required = false;

    /**
     * @var MetaSet
     * @ORM\ManyToOne(targetEntity="MetaSet", inversedBy="fields")
     * @ORM\JoinColumn(name="metaset_id", referencedColumnName="id")
     */
    private $metaset;
}