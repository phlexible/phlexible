<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ImageExtractor;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;

/**
 * Image extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ImageExtractorInterface
{
    /**
     * Check if requirements for image extractor are given
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Check if extractor supports the given asset
     *
     * @param ExtendedFileInterface $file
     *
     * @return bool
     */
    public function supports(ExtendedFileInterface $file);

    /**
     * Extract image from file
     *
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function extract(ExtendedFileInterface $file);
}
