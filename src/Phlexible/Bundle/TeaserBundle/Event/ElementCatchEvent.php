<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Event;

use Phlexible\Bundle\TeaserBundle\ElementCatch\ElementCatch;
use Symfony\Component\EventDispatcher\Event;

/**
 * Element catch event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementCatchEvent extends Event
{
    /**
     * @var ElementCatch
     */
    private $elementCatch;

    /**
     * @param ElementCatch $elementCatch
     */
    public function __construct(ElementCatch $elementCatch)
    {
        $this->elementCatch = $elementCatch;
    }

    /**
     * @return ElementCatch
     */
    public function getTreeId()
    {
        return $this->elementCatch;
    }
}
