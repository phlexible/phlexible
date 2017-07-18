<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaExtractor\ImageExtractor;

use FFMpeg\FFMpeg;
use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
<<<<<<< Updated upstream:src/Phlexible/Component/MediaExtractor/ImageExtractor/VideoConverterImageExtractor.php
 * Video converter extractor.
=======
 * FFMpeg image extractor
>>>>>>> Stashed changes:src/Phlexible/Component/MediaExtractor/ImageExtractor/FFMpegImageExtractor.php
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FFMpegImageExtractor implements ExtractorInterface
{
    /**
     * @var FFMpeg
     */
    private $ffmpeg;

    /**
     * @var string
     */
    private $tempDir;

    /**
     * @param FFMpeg $ffmpeg
     * @param string $tempDir
     */
    public function __construct(FFMpeg $ffmpeg, $tempDir)
    {
        $this->ffmpeg = $ffmpeg;
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
            $imageFilename = $this->tempDir.'/'.uniqid().'.jpg';

            $this->ffmpeg
                ->open($filename)
                ->frame(\FFMpeg\Coordinate\TimeCode::fromSeconds(5))
                ->save($imageFilename);
        } catch (\Exception $e) {
            $imageFile = null;
        }

        return $imageFilename;
    }
}
