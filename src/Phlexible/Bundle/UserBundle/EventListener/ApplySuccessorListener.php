<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\UserBundle\EventListener;

use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;
use Phlexible\Bundle\UserBundle\Model\GroupManagerInterface;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;

/**
 * Apply successor listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorListener
{
    /**
     * @var UserManagerInterface $userManager
     */
    private $userManager;

    /**
     * @var GroupManagerInterface
     */
    private $groupManager;

    /**
     * @param UserManagerInterface  $userManager
     * @param GroupManagerInterface $groupManager
     */
    public function __construct(UserManagerInterface $userManager, GroupManagerInterface $groupManager)
    {
        $this->userManager = $userManager;
        $this->groupManager = $groupManager;
    }

    /**
     * @param ApplySuccessorEvent $event
     */
    public function onApplySuccessor(ApplySuccessorEvent $event)
    {
        // TODO: createUserId/modifyUserId?
        return;
        $fromUser = $event->getFromUser();
        $toUser = $event->getToUser();

        $fromUserId = $fromUser->getId();
        $toUserId = $toUser->getId();

        foreach ($this->groupManager->findBy(array('createUserId' => $fromUserId)) as $user) {
            $user->setCreateUserId($toUserId);
        }
        foreach ($this->groupManager->findBy(array('modifyUserId' => $fromUserId)) as $user) {
            $user->setModifyUserId($toUserId);
        }
        foreach ($this->userManager->findBy(array('createUserId' => $fromUserId)) as $user) {
            $user->setCreateUserId($toUserId);
        }
        foreach ($this->userManager->findBy(array('modifyUserId' => $fromUserId)) as $user) {
            $user->setModifyUserId($toUserId);
        }
    }
}
