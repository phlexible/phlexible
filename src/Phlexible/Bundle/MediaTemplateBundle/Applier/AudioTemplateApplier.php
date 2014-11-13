<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Applier;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use FFMpeg\Media\Audio;
use Phlexible\Bundle\MediaTemplateBundle\Model\AudioTemplate;
use Psr\Log\LoggerInterface;

/**
 * Audio cache worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AudioTemplateApplier
{
    /**
     * @var FFMpeg
     */
    private $converter;

    /**
     * @param FFMpeg  $converter
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
     * @param AudioTemplate $template
     *
     * @return string
     */
    public function getExtension(AudioTemplate $template)
    {
        switch ($template->getParameter('format')) {
            case 'flac':
                return 'flac';

            case 'vorbis':
                return 'oga';

            case 'mp3':
            default:
                return 'mp3';
        }
    }

    /**
     * @param AudioTemplate $template
     *
     * @return string
     */
    public function getMimetype(AudioTemplate $template)
    {
        switch ($template->getParameter('format')) {
            case 'flac':
                return 'audio/flac';

            case 'vorbis':
                return 'audio/ogg';

            case 'mp3':
            default:
                return 'audio/mpeg';
        }
    }

    /**
     * @param AudioTemplate $template
     * @param string        $inFilename
     * @param string        $outFilename
     *
     * @return Audio
     */
    public function apply(AudioTemplate $template, $inFilename, $outFilename)
    {
        $audio = $this->converter->open($inFilename);

        if ($template->hasParameter('format', true)) {
            switch ($template->getParameter('format')) {
                case 'flac':
                    $format = new \FFMpeg\Format\Audio\Flac();
                    break;

                case 'vorbis':
                    $format = new \FFMpeg\Format\Audio\Vorbis();
                    break;

                case 'mp3':
                default:
                    $format = new \FFMpeg\Format\Audio\Mp3();
                    break;

            }
        } else {
            $format = new \FFMpeg\Format\Audio\Mp3();
        }

        if ($template->hasParameter('audio_bitrate', true)) {
            $format->setAudioKiloBitrate($template->getParameter('audio_bitrate'));
        }

        if ($template->hasParameter('audio_samplerate', true)) {
            $audio->filters()->resample($template->getParameter('audio_samplerate'));
            //$format->setAudioSamplingRate($template->getParameter('audio_samplerate'));
        }

        if ($template->hasParameter('audio_channels', true)) {
            //$format->setAudioChannels($template->getParameter('audio_channels'));
        }

        $audio->save($format, $outFilename);

        return $this->converter->open($outFilename);
    }
}
