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
use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Exception\LogicException;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Doctrine message manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageManager implements MessageManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $messageRepository;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param EntityManager            $entityManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(EntityManager $entityManager, SecurityContextInterface $securityContext)
    {
        $this->entityManager = $entityManager;
        $this->securityContext = $securityContext;

        $this->messageRepository = $entityManager->getRepository('PhlexibleMessageBundle:Message');
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->messageRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $orderBy = null)
    {
        return $this->messageRepository->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findByCriteria(Criteria $criteria, $order = null, $limit = null, $offset = null)
    {
        return $this->messageRepository->findByCriteria($criteria, $order, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function countByCriteria(Criteria $criteria)
    {
        return $this->messageRepository->countByCriteria($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        return $this->messageRepository->getFacets();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetsByCriteria(Criteria $criteria)
    {
        return $this->messageRepository->getFacetsByCriteria($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriorityNames()
    {
        return array(
            0 => 'low',
            1 => 'normal',
            2 => 'high',
            3 => 'urgent',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeNames()
    {
        return array(
            0 => 'info',
            1 => 'error',
            2 => 'audit',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function updateMessage(Message $message)
    {
        if (!$this->entityManager->isOpen()) {
            return;
        }

        if ($message->getId()) {
            throw new LogicException('Messages can\'t be updated.');
        }

        if (!$message->getUser()) {
            if ($this->securityContext->getToken() && $this->securityContext->getToken()->getUser() instanceof UserInterface) {
                $user = $this->securityContext->getToken()->getUser()->getDisplayName();
            } elseif (PHP_SAPI === 'cli') {
                $user = 'cli';
            } else {
                $user = 'unknown';
            }
            $message->setUser($user);
        }

        $this->entityManager->persist($message);
        $this->entityManager->flush($message);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMessage(Message $message)
    {
        $this->entityManager->remove($message);
        $this->entityManager->flush();
    }
}
