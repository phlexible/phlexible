<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Event;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Symfony\Component\EventDispatcher\Event;

/**
 * Siteroot event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootEvent extends Event
{
    /**
     * @var Siteroot
     */
    private $siteroot;

    /**
     * @param Siteroot $siteroot
     */
    public function __construct(Siteroot $siteroot)
    {
        $this->siteroot = $siteroot;
    }

    /**
     * @return Siteroot
     */
    public function getSiteroot()
    {
        return $this->siteroot;
    }
}