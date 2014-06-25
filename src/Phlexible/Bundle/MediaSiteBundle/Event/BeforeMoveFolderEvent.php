<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\MoveFolderAction;

/**
 * Before move folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeMoveFolderEvent extends AbstractActionEvent
{
    /**
     * @return MoveFolderAction
     */
    public function getAction()
    {
        return parent::getAction();
    }
}