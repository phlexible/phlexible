<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Entity\Repository\MessageRepository;
use Phlexible\Bundle\MessageBundle\Exception\LogicException;
use Phlexible\Bundle\MessageBundle\Model\MessageManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
     * @var MessageRepository
     */
    private $messageRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return MessageRepository
     */
    private function getMessageRepository()
    {
        if (null === $this->messageRepository) {
            $this->messageRepository = $this->entityManager->getRepository('PhlexibleMessageBundle:Message');
        }

        return $this->messageRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getMessageRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $orderBy = null)
    {
        return $this->getMessageRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findByCriteria(Criteria $criteria, $order = null, $limit = null, $offset = null)
    {
        return $this->getMessageRepository()->findByCriteria($criteria, $order, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function countByCriteria(Criteria $criteria)
    {
        return $this->getMessageRepository()->countByCriteria($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        return $this->getMessageRepository()->getFacets();
    }

    /**
     * {@inheritdoc}
     */
    public function getFacetsByCriteria(Criteria $criteria)
    {
        return $this->getMessageRepository()->getFacetsByCriteria($criteria);
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
            if (PHP_SAPI === 'cli') {
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
