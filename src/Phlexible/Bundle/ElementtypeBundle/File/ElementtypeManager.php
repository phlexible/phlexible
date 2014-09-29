<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\File;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeEvents;
use Phlexible\Bundle\ElementtypeBundle\ElementtypesMessage;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeEvent;
use Phlexible\Bundle\ElementtypeBundle\Exception\CreateCancelledException;
use Phlexible\Bundle\ElementtypeBundle\Exception\DeleteCancelledException;
use Phlexible\Bundle\ElementtypeBundle\Exception\UpdateCancelledException;
use Phlexible\Bundle\ElementtypeBundle\File\Loader\DumperInterface;
use Phlexible\Bundle\ElementtypeBundle\File\Loader\LoaderInterface;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
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
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var DumperInterface
     */
    private $dumper;

    /**
     * @var MessagePoster
     */
    private $messageService;

    /**
     * @param LoaderInterface          $loader
     * @param DumperInterface          $dumper
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster            $messageService
     */
    public function __construct(
        LoaderInterface $loader,
        DumperInterface $dumper,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messageService)
    {
        $this->loader = $loader;
        $this->dumper = $dumper;
        $this->dispatcher = $dispatcher;
        $this->messageService = $messageService;
    }

    /**
     * {@inheritdoc}
     */
    public function find($elementtypeId)
    {
        return $this->loader->load($elementtypeId);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByUniqueId($uniqueId)
    {
        return $this->loader->load($uniqueId);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->loader->loadAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateElementtype(Elementtype $elementtype, $flush = true)
    {
        if (!$elementtype->getId()) {
            $event = new ElementtypeEvent($elementtype);
            if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_CREATE, $event)->isPropagationStopped()) {
                throw new CreateCancelledException('Create canceled by callback.');
            }

            $elementtype->setId('bla');
            $this->dumper->dump($elementtype);

            $event = new ElementtypeEvent($elementtype);
            $this->dispatcher->dispatch(ElementtypeEvents::CREATE, $event);

            // post message
            $message = ElementtypesMessage::create('Element type "' . $elementtype->getId() . ' created.');
            $this->messageService->post($message);
        } else {
            $event = new ElementtypeEvent($elementtype);
            if ($this->dispatcher->dispatch(ElementtypeEvents::BEFORE_UPDATE, $event)->isPropagationStopped()) {
                throw new UpdateCancelledException('Update canceled by callback.');
            }

            $this->dumper->dump($elementtype);

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

        throw new \Exception('todo');

        // send message
        $message = ElementtypesMessage::create('Element type "' . $elementtype->getId() . '" deleted.');
        $this->messageService->post($message);

        // post event
        $event = new ElementtypeEvent($elementtype);
        $this->dispatcher->dispatch(ElementtypeEvents::DELETE, $event);

        return $this;
    }
}
