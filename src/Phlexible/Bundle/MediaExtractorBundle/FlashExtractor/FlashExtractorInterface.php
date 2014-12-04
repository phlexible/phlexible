<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\FlashExtractor;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;

/**
 * Flash extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface FlashExtractorInterface
{
    /**
     * Check if requirements for flash extractor are given
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
     * Extract flash from file
     *
     * @param ExtendedFileInterface $file
     *
     * @return bool
     */
    public function extract(ExtendedFileInterface $file);
}
