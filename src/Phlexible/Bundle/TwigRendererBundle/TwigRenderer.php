<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle;

use Phlexible\Bundle\ElementRendererBundle\DataProviderInterface;
use Phlexible\Bundle\ElementRendererBundle\RenderConfiguration;
use Phlexible\Bundle\ElementRendererBundle\RendererInterface;

/**
 * Twig renderer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TwigRenderer implements RendererInterface
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var string
     */
    private $templateDir;

    /**
     * @param \Twig_Environment     $twig
     * @param DataProviderInterface $dataProvider
     * @param string                $templateDir
     */
    public function __construct(\Twig_Environment $twig, DataProviderInterface $dataProvider, $templateDir)
    {
        $this->twig = $twig;
        $this->dataProvider = $dataProvider;
        $this->templateDir = $templateDir . '../views/';
    }

    /**
     * {@inheritdoc}
     */
    public function render(RenderConfiguration $renderConfiguration)
    {
        //$this->prepare();

        $data = (array) $this->dataProvider->provide($renderConfiguration);

        return $this->twig->render($renderConfiguration->get('template'), $data);
    }

    private function prepare()
    {
        /* @var $chainLoader \Twig_Loader_Chain */
        $chainLoader = $this->twig->getLoader();
        foreach ($chainLoader->getLoaders() as $chainedLoader) {
            if ($chainedLoader instanceof \Twig_Loader_Filesystem) {
                $loader = $chainedLoader;
                break;
            }
        }

        $paths = array(
            $this->templateDir . 'html/',
            $this->templateDir . 'web/html/',
            $this->templateDir . 'web/',
            $this->templateDir . 'all/',
        );

        foreach ($paths as $pathIndex => $path) {
            if (!file_exists($path)) {
                unset($paths[$pathIndex]);
            }
        }

        $loader->setPaths($paths);
    }
}