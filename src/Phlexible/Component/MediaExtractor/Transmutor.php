<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaExtractor;

use Phlexible\Component\MediaCache\Worker\InputDescriptor;
use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;

/**
 * Extractor service.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Transmutor
{
    /**
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

    /**
     * @var ExtractorInterface
     */
    private $extractor;

    /**
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param ExtractorInterface        $extractor
     */
    public function __construct(MediaTypeManagerInterface $mediaTypeManager, ExtractorInterface $extractor)
    {
        $this->mediaTypeManager = $mediaTypeManager;
        $this->extractor = $extractor;
    }

    /**
     * Transmute file to target format.
     *
     * @param InputDescriptor $input
     * @param string          $targetFormat
     *
     * @return string
     */
    public function transmute(InputDescriptor $input, $targetFormat)
    {
        if (!in_array($targetFormat, array('image', 'audio', 'video', 'flash', 'text'))) {
            return null;
        }

        $mediaType = $this->mediaTypeManager->find($input->getMediaType());

        return $this->extractor->extract($input, $mediaType, $targetFormat);
    }

    /**
     * Transmute file to image.
     *
     * @param InputDescriptor $input
     *
     * @return string
     */
    public function transmuteToImage(InputDescriptor $input)
    {
        return $this->transmute($input, 'image');
    }

    /**
     * Transmute file to audio.
     *
     * @param InputDescriptor $input
     *
     * @return string
     */
    public function transmuteToAudio(InputDescriptor $input)
    {
        return $this->transmute($input, 'audio');
    }

    /**
     * Transmute file to video.
     *
     * @param InputDescriptor $input
     *
     * @return string
     */
    public function transmuteToVideo(InputDescriptor $input)
    {
        return $this->transmute($input, 'video');
    }

    /**
     * Transmute file to flash.
     *
     * @param InputDescriptor $input
     *
     * @return string
     */
    public function transmuteToFlash(InputDescriptor $input)
    {
        return $this->transmute($input, 'flash');
    }

    /**
     * Transmute file to text.
     *
     * @param InputDescriptor $input
     *
     * @return string
     */
    public function transmuteToText(InputDescriptor $input)
    {
        return $this->transmute($input, 'text');
    }
}
