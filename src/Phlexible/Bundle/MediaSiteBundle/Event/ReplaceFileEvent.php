<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\ReplaceFileAction;

/**
 * Replace file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReplaceFileEvent extends AbstractActionEvent
{
    /**
     * @return ReplaceFileAction
     */
    public function getAction()
    {
        return parent::getAction();
    }
}