<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DataSourceBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\UserBundle\Event\ApplySuccessorEvent;

/**
 * Apply successor listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ApplySuccessorListener
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
     * Will be called after a user is deleted to cleanup uids
     *
     * @param ApplySuccessorEvent $event
     */
    public function onApplySuccessor(ApplySuccessorEvent $event)
    {
        $datasourceRepository = $this->entityManager->getRepository('PhlexibleDataSourceBundle:DataSource');

        $fromUser = $event->getFromUser();
        $toUser   = $event->getToUser();

        $fromUserId = $fromUser->getId();
        $toUserId   = $toUser->getId();

        foreach ($datasourceRepository->findByCreateUserId($fromUserId) as $datasource) {
            $datasource->setCreateUserId($toUserId);
        }

        foreach ($datasourceRepository->findByModifyUserId($fromUserId) as $datasource) {
            $datasource->setModifyUserId($toUserId);
        }

        $this->entityManager->flush();
    }
}