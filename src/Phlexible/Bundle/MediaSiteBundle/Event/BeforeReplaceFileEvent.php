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
 * Before replace file event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 *
 * @method ReplaceFileAction getAction()
 */
class BeforeReplaceFileEvent extends AbstractActionEvent
{
}