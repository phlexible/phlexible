<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\RenderConfigurator;

use Dwoo\Template\File;
use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Template configurator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateConfigurator implements ConfiguratorInterface
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
     * @param EventDispatcherInterface $dispatcher
     * @param LoggerInterface          $logger
     */
    public function __construct(
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function configure(Request $request, RenderConfiguration $renderConfiguration)
    {
        if (!$renderConfiguration->hasFeature('templateFile')) {
            return;
        }

        $templateFile = $renderConfiguration->get('templateFile');
        $template = $templateFile;//new File('/Users/swentz/Sites/phlexible/brainbits/templates/html/' . $templateFile . '.html.dwoo');

        $renderConfiguration
            ->addFeature('template')
            ->set('template', $template);
    }

}
