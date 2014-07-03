<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\CreateFolderAction;

/**
 * Before create folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeCreateFolderEvent extends AbstractActionEvent
{
    /**
     * @return CreateFolderAction
     */
    public function getAction()
    {
        return parent::getAction();
    }
}