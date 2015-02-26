<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaExtractor\Extractor;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtractorInterface
{
    /**
     * Check if extractor supports the given asset
     *
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     * @param string                $targetFormat
     *
     * @return bool
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat);

    /**
     * Extract from file
     *
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     * @param string                $targetFormat
     *
     * @return string
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat);
}
