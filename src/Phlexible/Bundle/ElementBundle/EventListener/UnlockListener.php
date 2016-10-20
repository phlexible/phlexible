<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\UserBundle\Event\UserEvent;

/**
 * Unlock listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class UnlockListener
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * User delete callback
     * Cleanup users locks
     *
     * @param UserEvent $event
     */
    public function onBeforeDeleteUser(UserEvent $event)
    {
        $user = $event->getUser();

        $locks = $this->entityManager->getRepository('PhlexibleElementBundle:ElementLock')
            ->findBy(['userId' => $user->getId()]);

        foreach ($locks as $lock) {
            $this->entityManager->remove($lock);
        }
    }
}
