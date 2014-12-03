<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;

/**
 * Extractor service
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Transmutor
{
    /**
     * @var array
     */
    private $imageTransmutors;

    /**
     * @var array
     */
    private $videoTransmutors;

    /**
     * @var array
     */
    private $audioTransmutors;

    /**
     * @var array
     */
    private $flashTransmutors;

    /**
     * @param array $imageTransmutors
     * @param array $videoTransmutors
     * @param array $audioTransmutors
     * @param array $flashTransmutors
     */
    public function __construct(array $imageTransmutors = [], array $videoTransmutors = [], array $audioTransmutors = [], array $flashTransmutors = [])
    {
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
        foreach ($this->imageTransmutors as $transmutor) {
            if ($transmutor->isAvailable() && $transmutor->supports($file)) {
                return $transmutor->extract($file);
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
        foreach ($this->audioTransmutors as $transmutor) {
            if ($transmutor->isAvailable() && $transmutor->supports($file)) {
                return $transmutor->extract($file);
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
        foreach ($this->videoTransmutors as $transmutor) {
            if ($transmutor->isAvailable() && $transmutor->supports($file)) {
                return $transmutor->extract($file);
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
        foreach ($this->flashTransmutors as $transmutor) {
            if ($transmutor->isAvailable() && $transmutor->supports($file)) {
                return $transmutor->extract($file);
            }
        }

        return null;
    }
}
