<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaExtractor\ImageExtractor;

use GetId3\GetId3Core;
use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Psr\Log\LoggerInterface;

/**
 * GetId3 image extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetId3ImageExtractor implements ExtractorInterface
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
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'image' && $mediaType->getCategory() === 'audio';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        $filename = $file->getPhysicalPath();
        $imageFile = null;

        try {
            $getId3 = new GetId3Core();
            $info = $getId3->analyze($filename);
            if (isset($info['comments']['picture'][0])) {
                //$Image='data:'.$info['comments']['picture'][0]['image_mime'].';charset=utf-8;base64,'.base64_encode($info['comments']['picture'][0]['data']);
                $imageString = $info['comments']['picture'][0]['data'];
                if ($imageString) {
                    $imageFile = $this->tempDir . 'dummy.jpg';

                    file_put_contents($imageFile, $imageString);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error('getID3 failed to read image from ID3 tag: ' . $e->getMessage());

            $imageFile = null;
        }

        return $imageFile;
    }
}
