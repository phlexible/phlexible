<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Model;

/**
 * Audio template
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
            'audio_bitrate'    => '',
            'audio_samplerate' => '',
            'audio_samplebits' => '',
            'audio_channels'   => '',
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
