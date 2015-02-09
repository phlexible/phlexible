<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\MessageBundle\Entity\Subscription;
use Phlexible\Bundle\MessageBundle\Model\SubscriptionManagerInterface;

/**
 * Doctrine subscription manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SubscriptionManager implements SubscriptionManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $subscriptionRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->subscriptionRepository = $entityManager->getRepository('PhlexibleMessageBundle:Subscription');
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new Subscription();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->subscriptionRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->subscriptionRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->subscriptionRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $orderBy = null)
    {
        return $this->subscriptionRepository->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function updateSubscription(Subscription $subscription)
    {
        $this->entityManager->persist($subscription);
        $this->entityManager->flush($subscription);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSubscription(Subscription $subscription)
    {
        $this->entityManager->remove($subscription);
        $this->entityManager->flush();
    }
}
