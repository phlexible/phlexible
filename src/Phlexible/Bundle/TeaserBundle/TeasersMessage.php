<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Teasers message.
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
