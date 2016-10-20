<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\ElementsMessage;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\Repository\ElementRepository;
use Phlexible\Bundle\ElementBundle\Event\DeleteElementEvent;
use Phlexible\Bundle\ElementBundle\Event\ElementEvent;
use Phlexible\Bundle\ElementBundle\Exception\CreateCancelledException;
use Phlexible\Bundle\ElementBundle\Exception\DeleteCancelledException;
use Phlexible\Bundle\ElementBundle\Exception\UpdateCancelledException;
use Phlexible\Bundle\ElementBundle\Model\ElementManagerInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementManager implements ElementManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @var ElementRepository
     */
    private $elementRepository;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster            $messagePoster
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher, MessagePoster $messagePoster)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->messagePoster = $messagePoster;
    }

    /**
     * @return ElementRepository
     */
    private function getElementRepository()
    {
        if (null === $this->elementRepository) {
            $this->elementRepository = $this->entityManager->getRepository('PhlexibleElementBundle:Element');
        }

        return $this->elementRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getElementRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getElementRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateElement(Element $element, $flush = true)
    {
        if (!$element->getEid()) {
            $event = new ElementEvent($element);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_CREATE_ELEMENT, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by listener.');
            }

            $this->entityManager->persist($element);

            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementEvent($element);
            $this->dispatcher->dispatch(ElementEvents::CREATE_ELEMENT, $event);

            // post message
            $message = ElementsMessage::create("Element {$element->getEid()} created.");
            $this->messagePoster->post($message);
        } else {
            $event = new ElementEvent($element);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_UPDATE_ELEMENT, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Update canceled by listener.');
            }

            $this->entityManager->persist($element);
            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementEvent($element);
            $this->dispatcher->dispatch(ElementEvents::UPDATE_ELEMENT, $event);

            // post message
            $message = ElementsMessage::create("Element {$element->getEid()} updated.");
            $this->messagePoster->post($message);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteElement(Element $element)
    {
        $eid = $element->getEid();

        $event = new DeleteElementEvent($element, $eid);
        if ($this->dispatcher->dispatch(ElementEvents::BEFORE_DELETE_ELEMENT, $event)->isPropagationStopped()) {
            throw new DeleteCancelledException('Delete canceled by listener.');
        }

        $this->entityManager->remove($element);
        $this->entityManager->flush();

        $event = new DeleteElementEvent($element, $eid);
        $this->dispatcher->dispatch(ElementEvents::DELETE_ELEMENT, $event);

        return $this;
    }
}
