<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendMediaBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementEvents;
use Phlexible\Bundle\ElementBundle\Event\DeleteElementEvent;
use Phlexible\Bundle\ElementBundle\Event\ElementVersionEvent;
use Phlexible\Bundle\FrontendMediaBundle\Usage\UsageUpdater;
use Phlexible\Bundle\QueueBundle\Entity\Job;
use Phlexible\Bundle\QueueBundle\Model\JobManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Element listener.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementListener implements EventSubscriberInterface
{
    /**
     * @var UsageUpdater
     */
    private $usageUpdater;

    /**
     * @var JobManagerInterface
     */
    private $jobManager;

    /**
     * @var bool
     */
    private $useJobs = false;

    /**
     * @param UsageUpdater        $usageUpdater
     * @param JobManagerInterface $jobManager
     */
    public function __construct(UsageUpdater $usageUpdater, JobManagerInterface $jobManager)
    {
        $this->usageUpdater = $usageUpdater;
        $this->jobManager = $jobManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ElementEvents::CREATE_ELEMENT_VERSION => 'onCreateElementVersion',
            ElementEvents::UPDATE_ELEMENT_VERSION => 'onUpdateElementVersion',
            ElementEvents::DELETE_ELEMENT => 'onDeleteElement',
            ElementEvents::COMMIT_CHANGES => 'onCommitChanges',
        ];
    }

    public function onCommitChanges()
    {
        $this->useJobs = true;
    }

    /**
     * @param ElementVersionEvent $event
     */
    public function onCreateElementVersion(ElementVersionEvent $event)
    {
        if (!$this->useJobs) {
            $this->usageUpdater->updateUsage($event->getElementVersion()->getElement());
        } else {
            $job = new Job('frontend-media:update-usage', array($event->getElementVersion()->getElement()->getEid()));
            $this->jobManager->updateJob($job);
        }
    }

    /**
     * @param ElementVersionEvent $event
     */
    public function onUpdateElementVersion(ElementVersionEvent $event)
    {
        if (!$this->useJobs) {
            $this->usageUpdater->updateUsage($event->getElementVersion()->getElement());
        } else {
            $job = new Job('frontend-media:update-usage', array($event->getElementVersion()->getElement()->getEid()));
            $this->jobManager->updateJob($job);
        }
    }

    /**
     * @param DeleteElementEvent $event
     */
    public function onDeleteElement(DeleteElementEvent $event)
    {
        $this->usageUpdater->removeUsage($event->getEid());
    }
}
