<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\ActionInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Action event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AbstractActionEvent extends Event
{
    /**
     * @var ActionInterface
     */
    private $action;

    /**
     * @param ActionInterface $action
     */
    public function __construct(ActionInterface $action)
    {
        $this->action = $action;
    }

    /**
     * @return ActionInterface
     */
    public function getAction()
    {
        return $this->action;
    }
}