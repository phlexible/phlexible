<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;

/**
 * Element structure collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeStructureCollection
{
    /**
     * @var ElementtypeStructure[]
     */
    private $structures;

    /**
     * @param ElementtypeStructure[] $structures
     */
    public function __construct(array $structures = array())
    {
        foreach ($structures as $structure) {
            $this->add($structure);
        }
    }

    /**
     * @param ElementtypeStructure $structure
     *
     * @return $this
     */
    public function add(ElementtypeStructure $structure)
    {
        $index = $structure->getElementTypeVersion()->getElementtype()->getId(
            ) . '___' . $structure->getElementTypeVersion()->getVersion();
        $this->structures[$index] = $structure;

        return $this;
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     *
     * @return ElementtypeStructure|null
     */
    public function get(ElementtypeVersion $elementtypeVersion)
    {
        $index = $elementtypeVersion->getElementtype()->getId() . '___' . $elementtypeVersion->getVersion();

        if (isset($this->structures[$index])) {
            return $this->structures[$index];
        }

        return null;
    }
}
