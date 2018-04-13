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

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Video converter extractor.
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
    public function supports(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'image' && $mediaType->getCategory() === 'video';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(InputDescriptor $input, MediaType $mediaType, $targetFormat)
    {
        $filename = $input->getFilePath();

        if (!file_exists($filename)) {
            return null;
        }

        $imageFilename = null;

        try {
            $imageFilename = $this->tempDir.'/'.uniqid('videoconverter-', true).'.jpg';

            $this->converter
                ->open($filename)
                ->frame(TimeCode::fromSeconds(5))
                ->save($imageFilename);
        } catch (\Exception $e) {
            $imageFile = null;
        }

        return $imageFilename;
    }
}
