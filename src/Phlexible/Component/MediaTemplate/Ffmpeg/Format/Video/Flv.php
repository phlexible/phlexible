<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Ffmpeg\Format\Video;

use FFMpeg\Format\Video\DefaultVideo;

/**
 * The Flv video format
 */
class Flv extends DefaultVideo
{
    /**
     * @param string $audioCodec
     * @param string $videoCodec
     */
    public function __construct($audioCodec = 'libmp3lame', $videoCodec = 'flv')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
    }

    /**
     * {@inheritDoc}
     */
    public function supportBFrames()
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableAudioCodecs()
    {
        return ['libmp3lame'];
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableVideoCodecs()
    {
        return ['flv'];
    }
}
