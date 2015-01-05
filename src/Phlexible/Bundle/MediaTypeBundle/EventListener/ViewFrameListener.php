<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTypeBundle\EventListener;

use Phlexible\Bundle\GuiBundle\Event\ViewEvent;
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
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param ViewEvent $event
     */
    public function onViewFrame(ViewEvent $event)
    {
        $view = $event->getView();

        $view
            ->addScript($this->router->generate('mediatypes_asset_scripts'))
            ->addLink($this->router->generate('mediatypes_asset_css'));
    }
}
