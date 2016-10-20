<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Media manager message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaManagerMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public static function getDefaultChannel()
    {
        return 'mediamanager';
    }

    /**
     * {@inheritdoc}
     */
    public static function getDefaultRole()
    {
        return 'ROLE_MEDIA';
    }
}
