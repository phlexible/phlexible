<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\AttributeMetaData;
use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Psr\Log\LoggerInterface;

/**
 * Zend media attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZendMediaAttributeReader implements AttributeReaderInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return class_exists('Zend_Media');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('assettype')) === 'audio';
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        $filename = $file->getPhysicalPath();

        $metaData = new AttributeMetaData();
        $metaData->setTitle('Media attributes');

        try {
            $abs = new \Zend_Media_Mpeg_Abs($filename);

            /* @var $frame \Zend_Media_Mpeg_Abs_Frame */
            $frame = current($abs->getFrames());

            $mpeg_version = array(
                \Zend_Media_Mpeg_Abs_Frame::VERSION_TWO_FIVE => "MPEG 2.5",
                \Zend_Media_Mpeg_Abs_Frame::VERSION_TWO      => "MPEG 2",
                \Zend_Media_Mpeg_Abs_Frame::VERSION_ONE      => "MPEG 1"
            );
            $mpeg_layer = array(
                \Zend_Media_Mpeg_Abs_Frame::LAYER_THREE => "Layer III",
                \Zend_Media_Mpeg_Abs_Frame::LAYER_TWO   => "Layer II",
                \Zend_Media_Mpeg_Abs_Frame::LAYER_ONE   => "Layer I"
            );
            $mpeg_mode = array(
                \Zend_Media_Mpeg_Abs_Frame::CHANNEL_STEREO         => "Stereo",
                \Zend_Media_Mpeg_Abs_Frame::CHANNEL_JOINT_STEREO   => "Joint Stereo",
                \Zend_Media_Mpeg_Abs_Frame::CHANNEL_DUAL_CHANNEL   => "Dual Channel",
                \Zend_Media_Mpeg_Abs_Frame::CHANNEL_SINGLE_CHANNEL => "Single Channel"
            );
            $mpeg_emphasis = array(
                \Zend_Media_Mpeg_Abs_Frame::EMPHASIS_NONE     => "No emphasis",
                \Zend_Media_Mpeg_Abs_Frame::EMPHASIS_50_15    => "50/15 microsec. emphasis",
                \Zend_Media_Mpeg_Abs_Frame::EMPHASIS_CCIT_J17 => "CCITT J.17"
            );
            $mpeg_boolean = array(
                0 => "No",
                1 => "Yes"
            );

            $metaData
                ->set('channelmode', $mpeg_mode[$frame->getMode()])
                ->set('version', $mpeg_version[$frame->getVersion()])
                ->set('layer', $mpeg_layer[$frame->getLayer()])
                ->set('emphasis', $mpeg_emphasis[$frame->getEmphasis()])
                ->set('copyright', $mpeg_boolean[$frame->getCopyright()])
                ->set('crc', $mpeg_boolean[$frame->getCrc()])
                ->set('original', $mpeg_boolean[$frame->getOriginal()]);

            if ($abs->getLengthEstimate()) {
                $metaData->set('playtime', $abs->getFormattedLengthEstimate());
            }
            elseif ($abs->getLength()) {
                $metaData->set('playtime', $abs->getFormattedLength());
            }

            if ($bitrate = $abs->getBitrate()) {
                if (strpos($bitrate, ',') !== false) {
                    $bitrate = str_replace(',', '.', $bitrate);
                }

                $metaData->set('bitrate', round($bitrate) . ' kbps');
            }
            elseif ($bitrate = $abs->getBitrateEstimate()) {
                if (strpos($bitrate, ',') !== false) {
                    $bitrate = str_replace(',', '.', $bitrate);
                }

                $metaData->set('bitrate', round($bitrate) . ' kbps');
            }

            if ($samplingFrequency = $frame->getSamplingFrequency()) {
                $metaData->set('samplerate', $samplingFrequency . ' Hz');
            }

//            if(!empty($getid3->info['audio']['bitrate_mode']))
//            {
//                $metaData->bitrate_mode = $getid3->info['audio']['bitrate_mode'];
//            }

//            if(!empty($getid3->info['audio']['codec']))
//            {
//                $metaData->codec = $getid3->info['audio']['codec'];
//            }

//            if(!empty($getid3->info['audio']['encoder']))
//            {
//                $metaData->encoder = $getid3->info['audio']['encoder'];
//            }

            $metaBag->add($metaData);
        } catch (\Exception $e) {
            $this->logger->error('ZendMediaAttributeReader failed to read attributes from asset: ' . $e->getMessage());
        }
    }
}