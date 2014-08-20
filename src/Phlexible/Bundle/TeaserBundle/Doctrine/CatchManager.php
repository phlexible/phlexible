<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\TeaserBundle\Entity\ElementCatch;
use Phlexible\Bundle\TeaserBundle\Event\ElementCatchEvent;
use Phlexible\Bundle\TeaserBundle\Model\CatchManagerInterface;
use Phlexible\Bundle\TeaserBundle\TeaserEvents;
use Phlexible\Bundle\TeaserBundle\TeasersMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Catch manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CatchManager implements CatchManagerInterface
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
        return $this->entityManager->getRepository('PhlexibleTeaserBundle:ElementCatch')->find($id);
    }

    /**
     * @param ElementCatch $catch
     */
    public function updateCatch(ElementCatch $catch)
    {
        if ($catch->getId()) {
            $event = new ElementCatchEvent($catch);
            if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_UPDATE_CATCH, $event)->isPropagationStopped()) {
                return;
            }

            $this->entityManager->flush($catch);

            // save event
            $event = new ElementCatchEvent($catch);
            $this->dispatcher->dispatch(TeaserEvents::UPDATE_CATCH, $event);

            // post cleartext message
            $message = TeasersMessage::create('Catch updated.');
            $this->messageService->post($message);
        } else {
            $event = new ElementCatchEvent($catch);
            if ($this->dispatcher->dispatch(TeaserEvents::BEFORE_CREATE_CATCH, $event)->isPropagationStopped()) {
                return;
            }

            $this->entityManager->persist($catch);
            $this->entityManager->flush($catch);

            // save event
            $event = new ElementCatchEvent($catch);
            $this->dispatcher->dispatch(TeaserEvents::CREATE_CATCH, $event);

            // post cleartext message
            $message = TeasersMessage::create('Catch created.');
            $this->messageService->post($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteCatch(ElementCatch $catch)
    {
        if ($catch->getId() === null) {
            return;
        }

        // post before delete event
        $event = new ElementCatchEvent($catch);
        if (!$this->dispatcher->dispatch(TeaserEvents::BEFORE_DELETE_CATCH, $event)) {
            return;
        }

        $this->entityManager->remove($catch);
        $this->entityManager->flush();

        // post delete event
        $event = new ElementCatchEvent($catch);
        $this->dispatcher->dispatch(TeaserEvents::DELETE_CATCH, $event);

        // post cleartext message
        $message = TeasersMessage::create("Catch {$catch->getTitle()} deleted.");
        $this->messageService->post($message);
    }

}