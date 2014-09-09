<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderConfig;
use Phlexible\Bundle\ElementFinderBundle\Event\ElementFinderConfigEvent;
use Phlexible\Bundle\ElementFinderBundle\Model\ElementFinderManagerInterface;
use Phlexible\Bundle\ElementFinderBundle\ElementFinderEvents;
use Phlexible\Bundle\ElementFinderBundle\ElementFinderMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Catch manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderManager implements ElementFinderManagerInterface
{
    const TYPE_TEASER = 'teaser';
    const TYPE_CATCH = 'catch';
    const TYPE_INHERITED = 'inherited';
    const TYPE_STOP = 'stop';
    const TYPE_HIDE = 'hide';

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
    private $messageService;

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
     * {@inheritdoc}
     */
    public function findCatch($id)
    {
        return $this->entityManager->getRepository('PhlexibleElementFinderBundle:ElementFinderConfig')->find($id);
    }

    /**
     * @param ElementFinderConfig $catch
     */
    public function updateCatch(ElementFinderConfig $catch)
    {
        if ($catch->getId()) {
            $event = new ElementFinderConfigEvent($catch);
            if ($this->dispatcher->dispatch(ElementFinderEvents::BEFORE_UPDATE_CATCH, $event)->isPropagationStopped()) {
                return;
            }

            $this->entityManager->flush($catch);

            // save event
            $event = new ElementFinderConfigEvent($catch);
            $this->dispatcher->dispatch(ElementFinderEvents::UPDATE_CATCH, $event);

            // post cleartext message
            $message = ElementFinderMessage::create('Catch updated.');
            $this->messageService->post($message);
        } else {
            $event = new ElementFinderConfigEvent($catch);
            if ($this->dispatcher->dispatch(ElementFinderEvents::BEFORE_CREATE_CATCH, $event)->isPropagationStopped()) {
                return;
            }

            $this->entityManager->persist($catch);
            $this->entityManager->flush($catch);

            // save event
            $event = new ElementFinderConfigEvent($catch);
            $this->dispatcher->dispatch(ElementFinderEvents::CREATE_CATCH, $event);

            // post cleartext message
            $message = ElementFinderMessage::create('Catch created.');
            $this->messageService->post($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCatch(ElementFinderConfig $catch)
    {
        if ($catch->getId() === null) {
            return;
        }

        // post before delete event
        $event = new ElementFinderConfigEvent($catch);
        if (!$this->dispatcher->dispatch(ElementFinderEvents::BEFORE_DELETE_CATCH, $event)) {
            return;
        }

        $this->entityManager->remove($catch);
        $this->entityManager->flush();

        // post delete event
        $event = new ElementFinderConfigEvent($catch);
        $this->dispatcher->dispatch(ElementFinderEvents::DELETE_CATCH, $event);

        // post cleartext message
        $message = ElementFinderMessage::create("Catch {$catch->getTitle()} deleted.");
        $this->messageService->post($message);
    }

}