<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ImageExtractor;

use GetId3\GetId3Core;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Psr\Log\LoggerInterface;

/**
 * GetId3 image extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GetId3ImageExtractor implements ImageExtractorInterface
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
        return class_exists('getID3');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file)
    {
        return strtolower($file->getAssettype()) === 'audio';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file)
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
