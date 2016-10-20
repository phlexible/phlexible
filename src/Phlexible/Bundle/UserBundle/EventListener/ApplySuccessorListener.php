<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

        foreach ($this->groupManager->findBy(['createUserId' => $fromUserId]) as $user) {
            $user->setCreateUserId($toUserId);
        }
        foreach ($this->groupManager->findBy(['modifyUserId' => $fromUserId]) as $user) {
            $user->setModifyUserId($toUserId);
        }
        foreach ($this->userManager->findBy(['createUserId' => $fromUserId]) as $user) {
            $user->setCreateUserId($toUserId);
        }
        foreach ($this->userManager->findBy(['modifyUserId' => $fromUserId]) as $user) {
            $user->setModifyUserId($toUserId);
        }
    }
}
