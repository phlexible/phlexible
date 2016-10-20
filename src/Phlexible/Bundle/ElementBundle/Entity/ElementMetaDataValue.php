<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaDataValue;

/**
 * Element meta
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @ORM\Entity
 * @ORM\Table(name="element_meta")
 */
class ElementMetaDataValue extends MetaDataValue
{
    /**
     * @var ElementVersion
     * @ORM\ManyToOne(targetEntity="ElementVersion")
     * @ORM\JoinColumn(name="element_version_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $elementVersion;

    /**
     * @param string         $setId
     * @param ElementVersion $elementVersion
     * @param string         $language
     * @param string         $fieldId
     */
    public function __construct($setId, ElementVersion $elementVersion, $language, $fieldId)
    {
        parent::__construct($setId, $language, $fieldId);

        $this->elementVersion = $elementVersion;
    }

    /**
     * @return ElementVersion
     */
    public function getElementVersion()
    {
        return $this->elementVersion;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return $this
     */
    public function setElementVersion($elementVersion)
    {
        $this->elementVersion = $elementVersion;

        return $this;
    }

}
