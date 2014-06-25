<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\QueueBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Queue message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class QueueMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array(
            'component' => 'queue',
        );
    }
}