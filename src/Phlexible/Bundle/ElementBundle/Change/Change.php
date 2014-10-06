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
     * @var ElementVersion
     */
    private $elementVersion;

    /**
     * @var Elementtype
     */
    private $elementtype;

    /**
     * @var string
     */
    private $revision;

    /**
     * @param ElementVersion $elementVersion
     * @param Elementtype    $elementtype
     * @param string         $revision
     */
    public function __construct(ElementVersion $elementVersion, Elementtype $elementtype, $revision)
    {
        $this->elementVersion = $elementVersion;
        $this->elementtype = $elementtype;
        $this->revision = $revision;
    }

    /**
     * @return ElementVersion
     */
    public function getElementVersion()
    {
        return $this->elementVersion;
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
