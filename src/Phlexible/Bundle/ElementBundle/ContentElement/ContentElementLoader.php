<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement;

use Phlexible\Bundle\ElementBundle\ContentElement\Loader\LoaderInterface;
use Phlexible\Bundle\ElementBundle\ContentElement\Loader\XmlLoader;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Content element loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentElementLoader
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var XmlLoader
     */
    private $loader;

    /**
     * @var ContentElement[]
     */
    private $elements = [];

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param LoaderInterface          $loader
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        LoaderInterface $loader)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->loader = $loader;
    }

    /**
     * @param int    $eid
     * @param int    $version
     * @param string $language
     *
     * @return ContentElement
     */
    public function load($eid, $version, $language)
    {
        $id = $eid.'_'.$language.'_'.$version;

        if (!isset($this->elements[$id])) {
            $this->elements[$id] = $this->loader->load($eid, $version, $language);
        }

        return $this->elements[$id];
    }

    /**
     * @return ContentElement[]
     */
    public function getElements()
    {
        return $this->elements;
    }
}
