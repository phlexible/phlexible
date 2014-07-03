<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement;

use Phlexible\Bundle\AccessControlBundle\Rights as ContentRightsManager;
use Phlexible\Bundle\ElementBundle\ContentElement\Dumper\XmlDumper;
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
     * @var ContentElementBuilder
     */
    private $builder;

    /**
     * @var XmlLoader
     */
    private $loader;

    /**
     * @var XmlDumper
     */
    private $dumper;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     * @param ContentElementBuilder    $builder
     * @param XmlLoader                $loader
     * @param XmlDumper                $dumper
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger,
        ContentElementBuilder $builder,
        XmlLoader $loader,
        XmlDumper $dumper)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
        $this->builder = $builder;
        $this->loader = $loader;
        $this->dumper = $dumper;
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
        $filename = $eid . '_' . $language . '.xml';

        $contentElement = $this->loader->load($filename);

        if (!$contentElement) {
            $contentElement = $this->builder->build($eid, $version, $language);
            $this->dumper->dump($contentElement, $filename);
        }

        return $contentElement;
    }
}
