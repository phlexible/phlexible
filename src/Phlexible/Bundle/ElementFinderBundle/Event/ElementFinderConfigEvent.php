<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Event;

use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element catch event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderConfigEvent extends Event
{
    /**
     * @var ElementFinderConfig
     */
    private $elementFinderConfig;

    /**
     * @param ElementFinderConfig $elementFinderConfig
     */
    public function __construct(ElementFinderConfig $elementFinderConfig)
    {
        $this->elementFinderConfig = $elementFinderConfig;
    }

    /**
     * @return ElementFinderConfig
     */
    public function getTreeId()
    {
        return $this->elementFinderConfig;
    }
}
