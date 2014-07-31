<?php
/**
 * phlexible
 *
 * @copyright 2007 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SecurityBundle\View;

use Phlexible\Bundle\GuiBundle\View\AbstractView;
use Phlexible\Bundle\SecurityBundle\Event\ViewEvent;
use Phlexible\Bundle\SecurityBundle\SecurityEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Validate email view
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ValidateEmailView extends AbstractView
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, SecurityContextInterface $securityContext)
    {
        $event = new ViewEvent($request, $this);
        $this->dispatcher->dispatch(SecurityEvents::VIEW_VALIDATE_EMAIL, $event);

        return $this;
    }
}