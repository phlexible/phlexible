<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ImageExtractor;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Image extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ImageExtractorInterface
{
    /**
     * Check if extractor supports the given asset
     *
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return bool
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType);

    /**
     * Extract image from file
     *
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return string
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType);
}
