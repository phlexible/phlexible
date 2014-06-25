<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle;

use Phlexible\Bundle\QueueBundle\Job\ContainerAwareJob;

/**
 * Write waiting job
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class WriteWaiting extends ContainerAwareJob
{
    public function work()
    {
        $this->getContainer()->get('mediacacheWorkerWaiting')->write();
    }
}
