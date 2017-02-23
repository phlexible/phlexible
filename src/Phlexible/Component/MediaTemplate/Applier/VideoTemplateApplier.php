<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Applier;

use FFMpeg\FFMpeg;
use FFMpeg\Media\Video;
use Phlexible\Component\MediaTemplate\Model\VideoTemplate;
use Psr\Log\LoggerInterface;

/**
 * Video template applier.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoTemplateApplier
{
    /**
     * @var FFMpeg
     */
    private $converter;

    /**
     * @param FFMpeg $converter
     */
    public function __construct(FFMpeg $converter)
    {
        $this->converter = $converter;
    }

    /**
     * @param string $filename
     *
     * @return bool
     */
    public function isAvailable($filename)
    {
        return true;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->converter->getFFMpegDriver()->getProcessRunner()->getLogger();
    }

    /**
     * @param VideoTemplate $template
     *
     * @return string
     */
    public function getExtension(VideoTemplate $template)
    {
        switch ($template->getParameter('format')) {
            case 'flv':
                return 'flv';

            case 'ogg':
                return 'ogv';

            case 'webm':
                return 'webm';

            case 'wmv':
            case 'wmv3':
                return 'wmv';

            case '3gp':
                return '3gp';

            case 'mp4':
            case 'x264':
            default:
                return 'mp4';
        }
    }

    /**
     * @param VideoTemplate $template
     *
     * @return string
     */
    public function getMimetype(VideoTemplate $template)
    {
        switch ($template->getParameter('format')) {
            case 'flv':
                return 'video/x-flv';

            case 'ogg':
                return 'video/ogg';

            case 'webm':
                return 'video/webm';

            case 'wmv':
            case 'wmv3':
                return 'video/wmv';

            case '3gp':
                return 'video/3gp';

            case 'mp4':
            case 'x264':
            default:
                return 'video/mp4';
        }
    }

    /**
     * @param VideoTemplate $template
     * @param string        $inFilename
     * @param string        $outFilename
     *
     * @return Video
     */
    public function apply(VideoTemplate $template, $inFilename, $outFilename)
    {
        $video = $this->converter->open($inFilename);

        if ($template->hasParameter('format', true)) {
            switch ($template->getParameter('format')) {
                case 'flv':
                    $format = new \Phlexible\Component\MediaTemplate\Ffmpeg\Format\Video\Flv();
                    break;

                case 'ogg':
                    $format = new \FFMpeg\Format\Video\Ogg();
                    break;

                case 'webm':
                    $format = new \FFMpeg\Format\Video\WebM();
                    break;

                case 'wmv':
                    $format = new \FFMpeg\Format\Video\WMV();
                    break;

                case 'wmv3':
                    $format = new \FFMpeg\Format\Video\WMV3();
                    break;

                case '3gp':
                    $format = new \FFMpeg\Format\Video\ThreeGP();
                    break;

                case 'mp4':
                case 'x264':
                default:
                    $format = new \FFMpeg\Format\Video\X264();
                    break;
            }
        } else {
            $format = new \FFMpeg\Format\Video\X264();
        }

        //if ($template->hasParameter('video_format', true)) {
        //    $format->setVideoCodec($template->getParameter('video_format'));
        //}

        if ($template->hasParameter('video_width', true) && $template->hasParameter('video_height', true)) {
            $width = $template->getParameter('video_width');
            $height = $template->getParameter('video_height');

            $video->filters()->resize(new \FFMpeg\Coordinate\Dimension($width, $height));
        }

        if ($template->hasParameter('video_bitrate', true)) {
            $format->setKiloBitrate($template->getParameter('video_bitrate'));
        }

        if ($template->hasParameter('video_framerate', true)) {
            $framerate = new \FFMpeg\Coordinate\FrameRate($template->getParameter('video_framerate'));
            $gop = 25;

            $video->filters()->framerate($framerate, $gop);
        }

        //if ($template->hasParameter('audio_format', true)) {
        //    $format->setAudioCodec($template->getParameter('audio_format'));
        //}

        if ($template->hasParameter('audio_bitrate', true)) {
            $format->setAudioKiloBitrate($template->getParameter('audio_bitrate'));
        }

        if ($template->hasParameter('audio_samplerate', true)) {
            $video->filters()->audioResample($template->getParameter('audio_samplerate'));
        }

        if ($template->hasParameter('audio_channels', true)) {
            //$format->set('channels', $template->getParameter('audio_channels'));
        }

        $video->save($format, $outFilename);

        return $this->converter->open($outFilename);
    }
}
