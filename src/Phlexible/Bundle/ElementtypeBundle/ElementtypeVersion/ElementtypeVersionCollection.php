<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;

/**
 * Elementtype version collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeVersionCollection
{
    /**
     * @var ElementtypeVersion[]
     */
    private $elementtypeVersions = array();

    /**
     * @var array
     */
    private $versions = array();

    /**
     * @param ElementtypeVersion[] $elementtypeVersions
     */
    public function __construct(array $elementtypeVersions = array())
    {
        foreach ($elementtypeVersions as $elementtypeVersion)
        {
            $this->add($elementtypeVersion);
        }
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     * @return $this
     */
    public function add(ElementtypeVersion $elementtypeVersion)
    {
        $index = $elementtypeVersion->getElementtype()->getId() . '___' . $elementtypeVersion->getVersion();
        $this->elementtypeVersions[$index] = $elementtypeVersion;

        return $this;
    }

    /**
     * @param Elementtype $elementtype
     * @param int         $version
     *
     * @return ElementtypeVersion|null
     */
    public function get(Elementtype $elementtype, $version)
    {
        $index = $elementtype->getId() . '___' . $version;

        if (isset($this->elementtypeVersions[$index])) {
            return $this->elementtypeVersions[$index];
        }

        return null;
    }

    /**
     * @return ElementtypeVersion[]
     */
    public function getAll()
    {
        return $this->elementtypeVersions;
    }

    /**
     * @param Elementtype $elementtype
     * @param array $versions
     * @return $this
     */
    public function setVersions(Elementtype $elementtype, array $versions)
    {
        $this->versions[$elementtype->getId()] = $versions;

        return $this;
    }

    /**
     * @param Elementtype $elementtype
     * @return null
     */
    public function getVersions(Elementtype $elementtype)
    {
        if (isset($this->versions[$elementtype->getId()]))
        {
            return $this->versions[$elementtype->getId()];
        }

        return null;
    }
}
