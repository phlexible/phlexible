<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Change;

use Phlexible\Bundle\ElementBundle\Entity\ElementSource;
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
     * @var ElementSource[]
     */
    private $outdatedElementSources = array();

    /**
     * @var Elementtype
     */
    private $elementtype;

    /**
     * @param Elementtype     $elementtype
     * @param ElementSource[] $outdatedElementSources
     */
    public function __construct(Elementtype $elementtype, array $outdatedElementSources = [])
    {
        $this->elementtype = $elementtype;

        foreach ($outdatedElementSources as $outdatedElementSource) {
            $this->addOutdatedElementSource($outdatedElementSource);
        }
    }

    /**
     * @return ElementSource[]
     */
    public function getOutdatedElementSources()
    {
        return $this->outdatedElementSources;
    }

    /**
     * @param ElementSource $outdatedElementSource
     *
     * @return $this
     */
    public function addOutdatedElementSource(ElementSource $outdatedElementSource)
    {
        $this->outdatedElementSources[] = $outdatedElementSource;

        return $this;
    }

    /**
     * @return Elementtype
     */
    public function getElementtype()
    {
        return $this->elementtype;
    }
}
