<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeEvents;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Loader\LoaderInterface;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeStructureEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element structure repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeStructureRepository
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
     * @var ElementtypeStructure[]
     */
    private $structures;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoaderInterface          $loader
     */
    public function __construct(EventDispatcherInterface $dispatcher, LoaderInterface $loader)
    {
        $this->dispatcher = $dispatcher;
        $this->loader = $loader;
    }

    /**
     * @return ElementtypeStructureCollection
     */
    public function getCollection()
    {
        if (null === $this->structures) {
            $this->structures = new ElementtypeStructureCollection();
        }

        return $this->structures;
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     *
     * @return ElementtypeStructure
     */
    public function find(ElementtypeVersion $elementtypeVersion)
    {
        $structure = $this->getCollection()->get($elementtypeVersion);

        if (null === $structure) {
            $structure = $this->loader->load($elementtypeVersion);
            $this->getCollection()->add($structure);
        }

        return $structure;
    }

    /**
     * @param ElementtypeStructure $elementtypeStructure
     *
     * @throws \Exception
     */
    public function save(ElementtypeStructure $elementtypeStructure)
    {
        $event = new ElementtypeStructureEvent($elementtypeStructure);
        if (!$this->dispatcher->dispatch(ElementtypeEvents::BEFORE_STRUCTURE_CREATE, $event)) {
            throw new \Exception('Canceled by listener.');
        }

        $this->loader->insert($elementtypeStructure);

        $event = new ElementtypeStructureEvent($elementtypeStructure);
        $this->dispatcher->dispatch(ElementtypeEvents::STRUCTURE_CREATE, $event);
    }
}
