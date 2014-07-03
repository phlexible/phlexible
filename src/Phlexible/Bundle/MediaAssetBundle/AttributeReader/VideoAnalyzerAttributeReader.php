<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use FFMpeg\FFProbe;
use FFMpeg\FFProbe\DataMapping\Stream;
use Phlexible\Bundle\MediaAssetBundle\AttributeMetaData;
use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Psr\Log\LoggerInterface;

/**
 * Video analyzer attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoAnalyzerAttributeReader implements AttributeReaderInterface
{
    /**
     * @var FFProbe
     */
    private $analyzer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param FFProbe         $analyzer
     * @param LoggerInterface $logger
     */
    public function __construct(FFProbe $analyzer, LoggerInterface $logger)
    {
        $this->analyzer = $analyzer;
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
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('assettype')) === 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        $metaData = new AttributeMetaData();
        $metaData->setTitle('Video attributes');

        try {
            $format = $this->analyzer->format($file->getPhysicalPath());

            if ($format->has('format_name')) {
                $metaData->set('format_name', $format->get('format_name'));
            }
            if ($format->has('format_long_name')) {
                $metaData->set('format_long_name', $format->get('format_long_name'));
            }
            if ($format->has('duration')) {
                $metaData->set('duration', $format->get('duration'));
            }
            if ($format->has('bit_rate')) {
                $metaData->set('bit_rate', $format->get('bit_rate'));
            }
            if ($format->has('width')) {
                $metaData->set('width', $format->get('width'));
            }
            if ($format->get('height')) {
                $metaData->set('height', $format->get('height'));
            }
            if ($format->get('nb_streams')) {
                $metaData->set('number_of_streams', $format->get('nb_streams'));
            }

            $streams = $this->analyzer->streams($file->getPhysicalPath());

            foreach ($streams as $stream) {
                /* @var $stream Stream */
                $index = $stream->get('index');
                $prefix = 'stream_' . $index . '_';

                if ($stream->has('codec_type')) {
                    $metaData->set($prefix . 'codec_type', $stream->get('codec_type'));
                }
                if ($stream->has('codec_name')) {
                    $metaData->set($prefix . 'codec_name', $stream->get('codec_name'));
                }
                if ($stream->has('codec_long_name')) {
                    $metaData->set($prefix . 'codec_long_name', $stream->get('codec_long_name'));
                }
                if ($stream->has('codec_time_base')) {
                    $metaData->set($prefix . 'codec_time_base', $stream->get('codec_time_base'));
                }
                if ($stream->has('codec_tag_string')) {
                    $metaData->set($prefix . 'codec_tag', $stream->get('codec_tag_string'));
                }
                if ($stream->has('bit_rate')) {
                    $metaData->set($prefix . 'bit_rate', $stream->get('bit_rate'));
                }
                if ($stream->isVideo()) {
                    if ($stream->has('display_aspect_ration')) {
                        $metaData->set($prefix . 'aspect_ratio', $stream->get('display_aspect_ratio'));
                    }
                    if ($stream->has('avg_frame_rate')) {
                        $metaData->set($prefix . 'frame_rate', $stream->get('avg_frame_rate'));
                    }
                } elseif ($stream->isAudio()) {
                    if ($stream->has('bits_per_sample')) {
                        $metaData->set($prefix . 'bits_per_sample', $stream->get('bits_per_sample'));
                    }
                    if ($stream->has('channels')) {
                        $metaData->set($prefix . 'channels', $stream->get('channels'));
                    }
                    if ($stream->has('avg_frame_rate')) {
                        $metaData->set($prefix . 'frame_rate', $stream->get('avg_frame_rate'));
                    }
                }
            }

            $metaBag->add($metaData);
        } catch (\Exception $e) {
            $this->logger->error('VideoAnalyzerAttributeReader failed to read attributes from asset: ' . $e->getMessage());
        }
    }
}
