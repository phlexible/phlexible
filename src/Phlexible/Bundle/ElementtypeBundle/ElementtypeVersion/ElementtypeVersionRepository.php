<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion;

use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeEvents;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\Loader\LoaderInterface;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeVersionEvent;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Elementtype version repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeVersionRepository
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MessagePoster
     */
    private $messageService;

    /**
     * @var ElementtypeVersionCollection
     */
    private $elementtypeVersions;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoaderInterface          $loader
     * @param MessagePoster           $messageService
     */
    public function __construct(EventDispatcherInterface $dispatcher, LoaderInterface $loader, MessagePoster $messageService)
    {
        $this->dispatcher = $dispatcher;
        $this->loader = $loader;
        $this->messageService = $messageService;
    }

    /**
     * @return ElementtypeVersionCollection
     */
    public function getCollection()
    {
        if (null === $this->elementtypeVersions)
        {
            $this->elementtypeVersions = new ElementtypeVersionCollection();
        }

        return $this->elementtypeVersions;
    }

    /**
     * @param Elementtype $elementtype
     * @param integer     $version
     * @return ElementtypeVersion
     */
    public function find(Elementtype $elementtype, $version = null)
    {
        if (null === $version) {
            $version = $elementtype->getLatestVersion();
        }

        $elementtypeVersion = $this->getCollection()->get($elementtype, $version);

        if (null === $elementtypeVersion) {
            $elementtypeVersion = $this->loader->load($elementtype, $version);
            $this->getCollection()->add($elementtypeVersion);
        }

        return $elementtypeVersion;
    }

    /**
     * @param Elementtype $elementtype
     * @return array
     */
    public function getVersions(Elementtype $elementtype)
    {
        $versions = $this->getCollection()->getVersions($elementtype);

        if (null === $versions) {
            $versions = $this->loader->loadVersions($elementtype);
            $this->getCollection()->setVersions($elementtype, $versions);
        }

        return $versions;
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     * @throws \Exception
     */
    public function save(ElementtypeVersion $elementtypeVersion)
    {
        $event = new ElementtypeVersionEvent($elementtypeVersion);
        if (!$this->dispatcher->dispatch(ElementtypeEvents::BEFORE_VERSION_CREATE, $event)) {
            throw new \Exception('Canceled by listener.');
        }

        $this->loader->insert($elementtypeVersion);

        $event = new ElementtypeVersionEvent($elementtypeVersion);
        $this->dispatcher->dispatch(ElementtypeEvents::VERSION_CREATE, $event);
    }
}
