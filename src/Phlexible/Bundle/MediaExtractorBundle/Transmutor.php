<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle;

use Phlexible\Bundle\MediaExtractorBundle\AudioExtractor\AudioExtractorInterface;
use Phlexible\Bundle\MediaExtractorBundle\FlashExtractor\FlashExtractorInterface;
use Phlexible\Bundle\MediaExtractorBundle\ImageExtractor\ImageExtractorInterface;
use Phlexible\Bundle\MediaExtractorBundle\VideoExtractor\VideoExtractorInterface;
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
     * @var ImageExtractorInterface[]
     */
    private $imageTransmutors;

    /**
     * @var VideoExtractorInterface[]
     */
    private $videoTransmutors;

    /**
     * @var AudioExtractorInterface[]
     */
    private $audioTransmutors;

    /**
     * @var FlashExtractorInterface[]
     */
    private $flashTransmutors;

    /**
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param ImageExtractorInterface[] $imageTransmutors
     * @param VideoExtractorInterface[] $videoTransmutors
     * @param AudioExtractorInterface[] $audioTransmutors
     * @param FlashExtractorInterface[] $flashTransmutors
     */
    public function __construct(
        MediaTypeManagerInterface $mediaTypeManager,
        array $imageTransmutors = [],
        array $videoTransmutors = [],
        array $audioTransmutors = [],
        array $flashTransmutors = []
    )
    {
        $this->mediaTypeManager = $mediaTypeManager;
        $this->imageTransmutors = $imageTransmutors;
        $this->audioTransmutors = $audioTransmutors;
        $this->videoTransmutors = $videoTransmutors;
        $this->flashTransmutors = $flashTransmutors;
    }

    /**
     * Transmute asset to image file
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function transmuteToImage(ExtendedFileInterface $file)
    {
        $mediaType = $this->mediaTypeManager->find($file->getMediaType());

        foreach ($this->imageTransmutors as $transmutor) {
            if ($transmutor->supports($file, $mediaType)) {
                return $transmutor->extract($file, $mediaType);
            }
        }

        return null;
    }

    /**
     * Transmute asset to audio file
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function transmuteToAudio(ExtendedFileInterface $file)
    {
        $mediaType = $this->mediaTypeManager->find($file->getMediaType());

        foreach ($this->audioTransmutors as $transmutor) {
            if ($transmutor->supports($file, $mediaType)) {
                return $transmutor->extract($file, $mediaType);
            }
        }

        return null;
    }

    /**
     * Transmute asset to video file
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function transmuteToVideo(ExtendedFileInterface $file)
    {
        $mediaType = $this->mediaTypeManager->find($file->getMediaType());

        foreach ($this->videoTransmutors as $transmutor) {
            if ($transmutor->supports($file, $mediaType)) {
                return $transmutor->extract($file, $mediaType);
            }
        }

        return null;
    }

    /**
     * Transmute asset to flash file
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function transmuteToFlash(ExtendedFileInterface $file)
    {
        $mediaType = $this->mediaTypeManager->find($file->getMediaType());

        foreach ($this->flashTransmutors as $transmutor) {
            if ($transmutor->supports($file, $mediaType)) {
                return $transmutor->extract($file, $mediaType);
            }
        }

        return null;
    }
}
