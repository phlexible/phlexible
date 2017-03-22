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
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Event\DeleteElementEvent;
use Phlexible\Bundle\ElementBundle\Event\ElementVersionEvent;
use Phlexible\Bundle\FrontendMediaBundle\Usage\UsageUpdater;
use Phlexible\Bundle\QueueBundle\Entity\Job;
use Phlexible\Bundle\QueueBundle\Model\JobManagerInterface;
use Phlexible\Bundle\TeaserBundle\Event\PublishTeaserEvent;
use Phlexible\Bundle\TeaserBundle\TeaserEvents;
use Phlexible\Bundle\TreeBundle\Event\PublishNodeEvent;
use Phlexible\Bundle\TreeBundle\TreeEvents;
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
     * @var ElementService
     */
    private $elementService;

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
     * @param ElementService      $elementService
     * @param JobManagerInterface $jobManager
     */
    public function __construct(UsageUpdater $usageUpdater, ElementService $elementService, JobManagerInterface $jobManager)
    {
        $this->usageUpdater   = $usageUpdater;
        $this->elementService = $elementService;
        $this->jobManager     = $jobManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            ElementEvents::CREATE_ELEMENT_VERSION => 'onCreateElementVersion',
            ElementEvents::UPDATE_ELEMENT_VERSION => 'onUpdateElementVersion',
            ElementEvents::DELETE_ELEMENT         => 'onDeleteElement',
            ElementEvents::COMMIT_CHANGES         => 'onCommitChanges',
            TreeEvents::PUBLISH_NODE              => 'onPublishNode',
            TeaserEvents::PUBLISH_TEASER          => 'onPublishTeaser',
        ];
    }

    public function onCommitChanges()
    {
        $this->useJobs = true;
    }

    public function onPublishNode(PublishNodeEvent $event)
    {
        $element = $this->elementService->findElement($event->getNode()->getTypeId());
        $this->updateUsage($element);
    }

    public function onPublishTeaser(PublishTeaserEvent $event)
    {
        $element = $this->elementService->findElement($event->getTeaser()->getTypeId());
        $this->updateUsage($element);
    }

    /**
     * @param ElementVersionEvent $event
     */
    public function onCreateElementVersion(ElementVersionEvent $event)
    {
        $this->updateUsage($event->getElementVersion()->getElement());
    }

    /**
     * @param ElementVersionEvent $event
     */
    public function onUpdateElementVersion(ElementVersionEvent $event)
    {
        $this->updateUsage($event->getElementVersion()->getElement());
    }

    /**
     * @param DeleteElementEvent $event
     */
    public function onDeleteElement(DeleteElementEvent $event)
    {
        $this->usageUpdater->removeUsage($event->getEid());
    }

    private function updateUsage(Element $element)
    {
        if (!$this->useJobs) {
            $this->usageUpdater->updateUsage($element);
        } else {
            $job = new Job('frontend-media:update-usage', [$element->getEid()]);
            $this->jobManager->updateJob($job);
        }
    }
}

