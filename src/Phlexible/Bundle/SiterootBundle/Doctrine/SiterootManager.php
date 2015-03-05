<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Event\SiterootEvent;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\SiterootBundle\SiterootEvents;
use Phlexible\Bundle\SiterootBundle\SiterootsMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Siteroot identifier
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class SiterootManager implements SiterootManagerInterface
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
     * @var EntityRepository
     */
    private $siterootRepository;

    /**
     * @var MessagePoster
     */
    private $messagePoster;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster            $messagePoster
     */
    public function __construct(
        EntityManager $entityManager,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messagePoster)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
        $this->messagePoster = $messagePoster;
    }

    /**
     * @return EntityRepository
     */
    private function getSiterootRepository()
    {
        if (null === $this->siterootRepository) {
            $this->siterootRepository = $this->entityManager->getRepository('PhlexibleSiterootBundle:Siteroot');
        }

        return $this->siterootRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->getSiterootRepository()->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->getSiterootRepository()->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateSiteroot(Siteroot $siteroot)
    {
        if ($this->entityManager->contains($siteroot)) {
            $event = new SiterootEvent($siteroot);
            if ($this->dispatcher->dispatch(SiterootEvents::BEFORE_UPDATE_SITEROOT, $event)->isPropagationStopped()) {
                return;
            }

            $this->entityManager->flush();

            $event = new SiterootEvent($siteroot);
            $this->dispatcher->dispatch(SiterootEvents::UPDATE_SITEROOT, $event);

            $message = SiterootsMessage::create('Siteroot updated.', '', null, null, 'siteroot');
            $this->messagePoster->post($message);
        } else {
            $event = new SiterootEvent($siteroot);
            if ($this->dispatcher->dispatch(SiterootEvents::BEFORE_CREATE_SITEROOT, $event)->isPropagationStopped()) {
                return;
            }

            if (null === $siteroot->getId()) {
                $this->applyIdentifier($siteroot);
            }

            $this->entityManager->persist($siteroot);
            $this->entityManager->flush();

            $event = new SiterootEvent($siteroot);
            $this->dispatcher->dispatch(SiterootEvents::CREATE_SITEROOT, $event);

            $message = SiterootsMessage::create('Siteroot created.', '', null, null, 'siteroot');
            $this->messagePoster->post($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteSiteroot(Siteroot $siteroot)
    {
        $event = new SiterootEvent($siteroot);
        if ($this->dispatcher->dispatch(SiterootEvents::BEFORE_DELETE_SITEROOT, $event)->isPropagationStopped()) {
            return;
        }

        $this->entityManager->remove($siteroot);
        $this->entityManager->flush();

        $event = new SiterootEvent($siteroot);
        $this->dispatcher->dispatch(SiterootEvents::DELETE_SITEROOT, $event);

        $message = SiterootsMessage::create('Siteroot deleted.', '', null, null, 'siteroot');
        $this->messagePoster->post($message);
    }

    /**
     * Apply UUID as identifier when entity doesn't have one yet.
     *
     * @param Siteroot $siteroot
     */
    private function applyIdentifier(Siteroot $siteroot)
    {
        $reflectionClass = new \ReflectionClass(get_class($siteroot));

        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($siteroot, Uuid::generate());
    }
}
