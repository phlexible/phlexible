<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
            ->findBy(array('userId' => $user->getId()));

        foreach ($locks as $lock) {
            $this->entityManager->remove($lock);
        }
    }

    /**
     * Logout callback
     *
     * @param LogoutEvent $event
     */
    public function onLogout(LogoutEvent $event)
    {
        $user = $event->getUser();

        $locks = $this->entityManager->getRepository('PhlexibleElementBundle:ElementLock')
            ->findBy(array('userId' => $user->getId(), 'type' => 'temporary'));

        foreach ($locks as $lock) {
            $this->entityManager->remove($lock);
        }
    }
}
