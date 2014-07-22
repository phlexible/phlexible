<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\FlashExtractor;

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
     * @param Asset $asset
     *
     * @return bool
     */
    public function supports(Asset $asset);

    /**
     * Extract flash from file
     *
     * @param Asset $asset
     *
     * @return bool
     */
    public function extract(Asset $asset);
}
