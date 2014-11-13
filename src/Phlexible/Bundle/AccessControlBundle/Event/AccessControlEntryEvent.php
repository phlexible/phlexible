<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Event;

use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;
use Symfony\Component\EventDispatcher\Event;

/**
 * Access control entry event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessControlEntryEvent extends Event
{
    /**
     * @var AccessControlEntry
     */
    private $accessControlEntry;

    /**
     * @param AccessControlEntry $accessControlEntry
     */
    public function __construct(AccessControlEntry $accessControlEntry)
    {
        $this->accessControlEntry = $accessControlEntry;
    }

    /**
     * @return AccessControlEntry
     */
    public function getAccessControlEntry()
    {
        return $this->accessControlEntry;
    }
}
