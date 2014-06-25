<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\ImageExtractor;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Psr\Log\LoggerInterface;

/**
 * Audio image extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ZendMediaImageExtractor implements ImageExtractorInterface
{
    /**
     * @var string
     */
    private $tempDir;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string          $tempDir
     * @param LoggerInterface $logger
     */
    public function __construct($tempDir, LoggerInterface $logger)
    {
        $this->tempDir = $tempDir;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return class_exists('Zend_Media_Id3v2');
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
    public function extract(FileInterface $file)
    {
        $filename = $file->getPhysicalPath();
        $imageFile = null;

        try {
            $id3   = new \Zend_Media_Id3v2($filename);
            $frame = $id3->getFramesByIdentifier('APIC'); // for attached picture

            if (!empty($frame[0])) {
                $imageString = $frame[0]->getImageData();

                if ($imageString) {
                    $imageFile = $this->tempDir . 'dummy.jpg';

                    file_put_contents($imageFile, $imageString);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('ZendMediaImageExtractor failed to read image from ID3 tag: ' . $e->getMessage());

            $imageFile = null;
        }

        return $imageFile;
    }
}
