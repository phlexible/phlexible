<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Model;

/**
 * Audio template.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AudioTemplate extends AbstractTemplate
{
    const TYPE_AUDIO = 'audio';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setType(self::TYPE_AUDIO);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return array(
            'audio_bitrate' => '',
            'audio_samplerate' => '',
            'audio_samplebits' => '',
            'audio_channels' => '',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedParameters()
    {
        return array(
            'audio_format',
            'audio_bitrate',
            'audio_samplerate',
            'audio_samplebits',
            'audio_channels',
        );
    }
}
