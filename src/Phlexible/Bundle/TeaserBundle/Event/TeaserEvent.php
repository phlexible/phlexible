<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Event;

use Phlexible\Bundle\TeaserBundle\Teaser\Teaser;
use Symfony\Component\EventDispatcher\Event;

/**
 * Teaser event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeaserEvent extends Event
{
    /**
     * @var Teaser
     */
    private $teaser;

    /**
     * @param Teaser $teaser
     */
    public function __construct(Teaser $teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * @return Teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }
}
