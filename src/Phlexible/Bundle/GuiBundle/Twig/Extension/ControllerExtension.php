<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;

/**
 * Twig controller extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ControllerExtension extends \Twig_Extension
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('component', array($this, 'componentFunction')),
            new \Twig_SimpleFunction('extjs', array($this, 'extjsFunction')),
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function componentFunction($path)
    {
        return '/bundles' . $path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function extjsFunction($path)
    {
        return $this->componentFunction('/phlexiblegui/scripts/ext-2.3.0' . $path);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'controller_extension';
    }
}