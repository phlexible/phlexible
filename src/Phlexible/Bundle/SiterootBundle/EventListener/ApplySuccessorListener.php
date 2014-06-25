<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\EventListener;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;

/**
 * Siteroots callbacks
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorListener
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @param SiterootManagerInterface $siterootManager
     */
    public function __construct(SiterootManagerInterface $siterootManager)
    {
        $this->siterootManager = $siterootManager;
    }

    public function onApplySuccessor(ApplySuccessorEvent $event)
    {
        $fromUser = $event->getFromUser();
        $toUser   = $event->getToUser();

        $fromUid = $fromUser->getId();
        $toUid   = $toUser->getId();

        foreach ($this->siterootManager->findAll() as $siteroot) {
            $changed = false;
            if ($siteroot->getCreateUserId() === $fromUid) {
                $changed = true;
                $siteroot->setCreateUserId($toUid);
            }
            if ($siteroot->getModifyUserId() === $fromUid) {
                $changed = true;
                $siteroot->setModifyUserId($toUid);
            }
            if ($changed) {
                $this->siterootManager->updateSiteroot($siteroot);
            }
        }
    }
}