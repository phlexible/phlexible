<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaExtractor\ImageExtractor;

use FFMpeg\FFMpeg;
use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Video converter extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class VideoConverterImageExtractor implements ExtractorInterface
{
    /**
     * @var FFMpeg
     */
    private $converter;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @param FFMpeg $converter
     * @param string $tempDir
     */
    public function __construct(FFMpeg $converter, $tempDir)
    {
        $this->converter = $converter;
        $this->tempDir = $tempDir;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'image' && $mediaType->getCategory() === 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        $filename = $file->getPhysicalPath();

        if (!file_exists($filename)) {
            return null;
        }

        $imageFilename = null;

        try {
            $imageFilename = $this->tempDir . '/' . uniqid() . '.jpg';

            $this->converter
                ->open($filename)
                ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(5))
                ->save($imageFilename);
        } catch (\Exception $e) {
            $imageFile = null;
        }

        return $imageFilename;
    }
}
