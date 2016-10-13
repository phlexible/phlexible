<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
