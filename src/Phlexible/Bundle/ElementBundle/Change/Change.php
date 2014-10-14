<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;

/**
 * Elementtype change
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Change
{
    /**
     * @var ElementVersion[]
     */
    private $elementVersions;

    /**
     * @var Elementtype
     */
    private $elementtype;

    /**
     * @var string
     */
    private $revision;

    /**
     * @param Elementtype      $elementtype
     * @param string           $revision
     * @param ElementVersion[] $elementVersions
     */
    public function __construct(Elementtype $elementtype, $revision, array $elementVersions = array())
    {
        $this->elementtype = $elementtype;
        $this->revision = $revision;
        $this->elementVersions = $elementVersions;
    }

    /**
     * @return ElementVersion[]
     */
    public function getElementVersions()
    {
        return $this->elementVersions;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return $this
     */
    public function addElementVersion(ElementVersion $elementVersion)
    {
        $this->elementVersions[] = $elementVersion;

        return $this;
    }

    /**
     * @return Elementtype
     */
    public function getElementtype()
    {
        return $this->elementtype;
    }

    /**
     * @return string
     */
    public function getRevision()
    {
        return $this->revision;
    }
}
