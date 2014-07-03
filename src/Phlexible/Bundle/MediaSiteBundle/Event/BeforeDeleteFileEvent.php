<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\DeleteFileAction;

/**
 * Before delete file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BeforeDeleteFileEvent extends AbstractActionEvent
{
    /**
     * @return DeleteFileAction
     */
    public function getAction()
    {
        return parent::getAction();
    }
}