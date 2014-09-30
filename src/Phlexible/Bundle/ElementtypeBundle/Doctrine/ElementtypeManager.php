<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeEvents;
use Phlexible\Bundle\ElementtypeBundle\ElementtypesMessage;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Entity\Repository\ElementtypeRepository;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeEvent;
use Phlexible\Bundle\ElementtypeBundle\Exception\CreateCancelledException;
use Phlexible\Bundle\ElementtypeBundle\Exception\DeleteCancelledException;
use Phlexible\Bundle\ElementtypeBundle\Exception\UpdateCancelledException;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeManagerInterface;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Elementtype manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeManager implements ElementtypeManagerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var MessagePoster
     */
    private $messageService;

    /**
     * ElementtypeRepository
     */
    private $elementtypeRepository;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster            $messageService
     */
    public function __construct(
        EntityManager $entityManager,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messageService)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->messageService = $messageService;
    }

    /**
     * @return ElementtypeRepository
     */
    private function getElementtypeRepository()
    {
        if (null === $this->elementtypeRepository) {
            $this->elementtypeRepository = $this->entityManager->getRepository('PhlexibleElementtypeBundle:Elementtype');
        }

        return $this->elementtypeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($elementtypeId)
    {
        return $this->getElementtypeRepository()->find($elementtypeId);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUniqueId($uniqueId)
    {
        return $this->getElementtypeRepository()->findOneBy(array('uniqueId' => $uniqueId, 'deleted' => 0));
    }

    /**
     * {@inheritdoc}
     */
    public function findByType($type)
    {
        return $this->getElementtypeRepository()->findBy(array('type' => $type, 'deleted' => 0));
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getElementtypeRepository()->findBy(array('deleted' => 0));
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementtype(Elementtype $elementtype, $flush = true)
    {
        if (!$this->entityManager->contains($elementtype)) {
            $event = new ElementtypeEvent($elementtype);
            if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_CREATE, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by callback.');
            }

            $this->entityManager->persist($elementtype);
            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementtypeEvent($elementtype);
            $this->dispatcher->dispatch(ElementtypeEvents::CREATE, $event);

            // post message
            $message = ElementtypesMessage::create('Element type "' . $elementtype->getId() . ' created.');
            $this->messageService->post($message);
        } else {
            $event = new ElementtypeEvent($elementtype);
            if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_UPDATE, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Create canceled by callback.');
            }

            $this->entityManager->persist($elementtype);
            if ($flush) {
                $this->entityManager->flush();
            }

            $event = new ElementtypeEvent($elementtype);
            $this->dispatcher->dispatch(ElementtypeEvents::UPDATE, $event);

            // post message
            $message = ElementtypesMessage::create('Element type "' . $elementtype->getId() . ' updated.');
            $this->messageService->post($message);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteElementtype(Elementtype $elementtype, $flush = true)
    {
        // post before event
        $event = new ElementtypeEvent($elementtype);
        if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_DELETE, $event)->isPropagationStopped()) {
            throw new DeleteCancelledException('Delete canceled by listener.');
        }

        $this->entityManager->remove($elementtype);

        if ($flush) {
            $this->entityManager->flush();
        }

        // send message
        $message = ElementtypesMessage::create('Element type "' . $elementtype->getId() . '" deleted.');
        $this->messageService->post($message);

        // post event
        $event = new ElementtypeEvent($elementtype);
        $this->dispatcher->dispatch(ElementtypeEvents::DELETE, $event);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function softDeleteElementtype(Elementtype $elementtype, $flush = true)
    {
        // post before event
        $event = new ElementtypeEvent($elementtype);
        if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_SOFT_DELETE, $event)->isPropagationStopped()) {
            throw new DeleteCancelledException('Delete canceled by listener.');
        }

        $elementtype->setDeleted(true);

        if ($flush) {
            $this->entityManager->flush($elementtype);
        }

        // send message
        $message = ElementtypesMessage::create('Element type "' . $elementtype->getId() . '" soft deleted.');
        $this->messageService->post($message);

        // post event
        $event = new ElementtypeEvent($elementtype);
        $this->dispatcher->dispatch(ElementtypeEvents::SOFT_DELETE, $event);

        return $this;
    }
}
