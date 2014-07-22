<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Event;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\CreateFileAction;

/**
 * Before create file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @method CreateFileAction getAction()
 */
class BeforeCreateFileEvent extends AbstractActionEvent
{
}