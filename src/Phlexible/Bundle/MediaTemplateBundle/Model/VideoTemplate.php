<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Model;

/**
 * Video template
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoTemplate extends AbstractTemplate
{
    const TYPE_VIDEO = 'video';

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->setType(self::TYPE_VIDEO);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultParameters()
    {
        return [
            'match_format'     => 0,
            'for_web'          => 0,
            'format'           => 'flv',
            'deinterlace'      => 'flv',
            'video_width'      => 0,
            'video_height'     => 0,
            'video_format'     => 'flv',
            'video_bitrate'    => 0,
            'video_framerate'  => 0,
            'audio_format'     => 'mp3',
            'audio_bitrate'    => 0,
            'audio_samplerate' => 0,
            'audio_samplebits' => 0,
            'audio_channels'   => 0,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedParameters()
    {
        return [
            'match_format',
            'for_web',
            'format',
            'deinterlace',
            'video_width',
            'video_height',
            'video_format',
            'video_bitrate',
            'video_framerate',
            'audio_format',
            'audio_bitrate',
            'audio_samplerate',
            'audio_samplebits',
            'audio_channels',
        ];
    }

    /**
     * Set width
     *
     * @param int $width
     *
     * @return $this
     */
    public function setWidth($width)
    {
        return $this->setParameter('video_width', $width);
    }

    /**
     * Return width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->getParameter('video_width');
    }

    /**
     * Set height
     *
     * @param int $height
     *
     * @return $this
     */
    public function setHeight($height)
    {
        return $this->setParameters('video_height', $height);
    }

    /**
     * Return height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->getParameter('video_height');
    }
}