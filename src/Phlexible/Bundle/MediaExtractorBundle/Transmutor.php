<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle;

use Phlexible\Bundle\MediaExtractorBundle\Extractor\ExtractorInterface;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;

/**
 * Extractor service
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
     * Transmute file to target format
     *
     * @param ExtendedFileInterface $file
     * @param string                $targetFormat
     *
     * @return string
     */
    public function transmute(ExtendedFileInterface $file, $targetFormat)
    {
        if (!in_array($targetFormat, array('image', 'audio', 'video', 'flash', 'text'))) {
            return null;
        }

        $mediaType = $this->mediaTypeManager->find($file->getMediaType());

        return $this->extractor->extract($file, $mediaType, $targetFormat);
    }

    /**
     * Transmute file to image
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function transmuteToImage(ExtendedFileInterface $file)
    {
        return $this->transmute($file, 'image');
    }

    /**
     * Transmute file to audio
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function transmuteToAudio(ExtendedFileInterface $file)
    {
        return $this->transmute($file, 'audio');
    }

    /**
     * Transmute file to video
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function transmuteToVideo(ExtendedFileInterface $file)
    {
        return $this->transmute($file, 'video');
    }

    /**
     * Transmute file to flash
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function transmuteToFlash(ExtendedFileInterface $file)
    {
        return $this->transmute($file, 'flash');
    }

    /**
     * Transmute file to text
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function transmuteToText(ExtendedFileInterface $file)
    {
        return $this->transmute($file, 'text');
    }
}
