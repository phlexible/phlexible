<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaManager\AttributeReader;

use FFMpeg\FFProbe;
use FFMpeg\FFProbe\DataMapping\Stream;
use Phlexible\Component\MediaType\Model\MediaType;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;
use Psr\Log\LoggerInterface;

/**
 * Video analyzer attribute reader.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FFProbeAttributeReader implements AttributeReaderInterface
{
    /**
     * @var FFProbe
     */
    private $ffprobe;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FFProbe         $ffprobe
     * @param LoggerInterface $logger
     */
    public function __construct(FFProbe $ffprobe, LoggerInterface $logger)
    {
        $this->ffprobe = $ffprobe;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(PathSourceInterface $fileSource, MediaType $mediaType)
    {
        return in_array($mediaType->getCategory(), array('video', 'audio'));
    }

    /**
     * {@inheritdoc}
     */
    public function read(PathSourceInterface $fileSource, MediaType $mediaType, AttributeBag $attributes)
    {
        $filename = $fileSource->getPath();

        try {
            $format = $this->ffprobe->format($filename);

            if ($format->has('format_name')) {
                $attributes->set('media.format_name', $format->get('format_name'));
            }
            if ($format->has('format_long_name')) {
                $attributes->set('media.format_long_name', $format->get('format_long_name'));
            }
            if ($format->has('duration')) {
                $attributes->set('media.duration', $format->get('duration'));
            }
            if ($format->has('bit_rate')) {
                $attributes->set('media.bit_rate', $format->get('bit_rate'));
            }
            if ($format->has('width')) {
                $attributes->set('media.width', $format->get('width'));
            }
            if ($format->has('height')) {
                $attributes->set('media.height', $format->get('height'));
            }
            if ($format->has('nb_streams')) {
                $attributes->set('media.number_of_streams', $format->get('nb_streams'));
            }

            $streams = $this->ffprobe->streams($filename);

            foreach ($streams as $stream) {
                /* @var $stream Stream */
                $index = $stream->get('index');
                $prefix = 'stream_'.$index;

                $type = 'media';
                if ($stream->isVideo()) {
                    $type = 'video';
                } elseif ($stream->isAudio()) {
                    $type = 'audio';
                }

                if ($stream->has('codec_type')) {
                    $attributes->set("$type.$prefix.codec_type", $stream->get('codec_type'));
                }
                if ($stream->has('codec_name')) {
                    $attributes->set("$type.$prefix.codec_name", $stream->get('codec_name'));
                }
                if ($stream->has('codec_long_name')) {
                    $attributes->set("$type.$prefix.codec_long_name", $stream->get('codec_long_name'));
                }
                if ($stream->has('codec_time_base')) {
                    $attributes->set("$type.$prefix.codec_time_base", $stream->get('codec_time_base'));
                }
                if ($stream->has('codec_tag_string')) {
                    $attributes->set("$type.$prefix.codec_tag", $stream->get('codec_tag_string'));
                }
                if ($stream->has('bit_rate')) {
                    $attributes->set("$type.$prefix.bit_rate", $stream->get('bit_rate'));
                }
                if ($stream->has('display_aspect_ration')) {
                    $attributes->set("$type.$prefix.aspect_ratio", $stream->get('display_aspect_ratio'));
                }
                if ($stream->has('avg_frame_rate')) {
                    $attributes->set("$type.$prefix.frame_rate", $stream->get('avg_frame_rate'));
                }
                if ($stream->has('bits_per_sample')) {
                    $attributes->set("$type.$prefix.bits_per_sample", $stream->get('bits_per_sample'));
                }
                if ($stream->has('channels')) {
                    $attributes->set("$type.$prefix.channels", $stream->get('channels'));
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('VideoAnalyzerAttributeReader failed to read attributes from asset: '.$e->getMessage());
        }
    }
}
