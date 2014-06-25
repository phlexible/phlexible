<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Extension;

use Phlexible\Bundle\FrontendAssetBundle\Collector\Collector;
use Phlexible\Bundle\TwigRendererBundle\TokenParser\ScriptsTokenParser;
use Phlexible\Bundle\TwigRendererBundle\TokenParser\StylesTokenParser;

/**
 * Twig asset extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AssetExtension extends \Twig_Extension
{
    /**
     * @var Collector
     */
    private $frontendAssetsCollector;

    /**
     * @param Collector $frontendAssetsCollector
     */
    public function __construct(Collector $frontendAssetsCollector)
    {
        $this->frontendAssetsCollector = $frontendAssetsCollector;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('asset', array($this, 'getAssetUrl')),
            new \Twig_SimpleFunction('asset_version', array($this, 'getAssetsVersion')),
        );
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return \Twig_TokenParser[] An array of Twig_TokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
            new ScriptsTokenParser($this->frontendAssetsCollector),
            new StylesTokenParser($this->frontendAssetsCollector),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getAssetUrl($path)
    {
        return $path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getAssetsVersion($path)
    {
        return $path;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'assets';
    }
}