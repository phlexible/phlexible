<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTypeBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\ViewEvent;
use Symfony\Bundle\FrameworkBundle\Templating\Helper\AssetsHelper;
use Symfony\Component\Routing\RouterInterface;

/**
 * View frame listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ViewFrameListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var AssetsHelper
     */
    private $assetsHelper;

    /**
     * @param RouterInterface $router
     * @param AssetsHelper    $assetsHelper
     */
    public function __construct(RouterInterface $router, AssetsHelper $assetsHelper)
    {
        $this->router = $router;
        $this->assetsHelper = $assetsHelper;
    }

    /**
     * @param ViewEvent $event
     */
    public function onViewFrame(ViewEvent $event)
    {
        $view = $event->getView();

        $view
            ->addScript($this->assetsHelper->getUrl($this->router->generate('mediatypes_asset_scripts')))
            ->addLink($this->assetsHelper->getUrl($this->router->generate('mediatypes_asset_css')));
    }
}
