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
use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\ElementBundle\Entity\Repository\ElementLinkRepository;
use Phlexible\Bundle\ElementBundle\Entity\Repository\ElementRepository;
use Phlexible\Bundle\ElementBundle\Event\DeleteElementLinkEvent;
use Phlexible\Bundle\ElementBundle\Event\ElementLinkEvent;
use Phlexible\Bundle\ElementBundle\Exception\CreateCancelledException;
use Phlexible\Bundle\ElementBundle\Exception\DeleteCancelledException;
use Phlexible\Bundle\ElementBundle\Exception\UpdateCancelledException;
use Phlexible\Bundle\ElementBundle\Model\ElementLinkManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element link manager.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementLinkManager implements ElementLinkManagerInterface
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
     * @var ElementRepository
     */
    private $elementLinkRepository;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return ElementLinkRepository
     */
    private function getElementLinkRepository()
    {
        if (null === $this->elementLinkRepository) {
            $this->elementLinkRepository = $this->entityManager->getRepository(ElementLink::class);
        }

        return $this->elementLinkRepository;
    }

    public function find($id)
    {
        return $this->getElementLinkRepository()->find($id);
    }

    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getElementLinkRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function updateElementLink(ElementLink $elementLink, $flush = true)
    {
        if (!$elementLink->getId()) {
            $event = new ElementLinkEvent($elementLink);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_CREATE_ELEMENT_LINK, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by listener.');
            }

            $this->entityManager->persist($elementLink);

            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementLinkEvent($elementLink);
            $this->dispatcher->dispatch(ElementEvents::CREATE_ELEMENT_LINK, $event);
        } else {
            $event = new ElementLinkEvent($elementLink);
            if ($this->dispatcher->dispatch(ElementEvents::BEFORE_UPDATE_ELEMENT_LINK, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Update canceled by listener.');
            }

            $this->entityManager->persist($elementLink);
            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementLinkEvent($elementLink);
            $this->dispatcher->dispatch(ElementEvents::UPDATE_ELEMENT_LINK, $event);
        }
    }

    public function updateElementLinks(array $elementLinks, $flush = true)
    {
        foreach ($elementLinks as $elementLink) {
            $this->updateElementLink($elementLink, false);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    public function deleteElementLink(ElementLink $elementLink, $flush = true)
    {
        $id = $elementLink->getId();

        $event = new DeleteElementLinkEvent($elementLink, $id);
        if ($this->dispatcher->dispatch(ElementEvents::BEFORE_DELETE_ELEMENT_LINK, $event)->isPropagationStopped()) {
            throw new DeleteCancelledException('Delete canceled by listener.');
        }

        $this->entityManager->remove($elementLink);
        if ($flush) {
            $this->entityManager->flush();
        }

        $event = new DeleteElementLinkEvent($elementLink, $id);
        $this->dispatcher->dispatch(ElementEvents::DELETE_ELEMENT_LINK, $event);
    }
}
