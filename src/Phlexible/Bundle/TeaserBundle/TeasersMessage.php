<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Teasers message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TeasersMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return [
            'channel' => 'teaser',
        ];
    }
}
