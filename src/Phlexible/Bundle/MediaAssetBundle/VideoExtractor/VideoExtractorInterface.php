<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\VideoExtractor;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Video extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface VideoExtractorInterface
{
    /**
     * Check if requirements for video extractor are given
     *
     * @return boolean
     */
    public function isAvailable();

    /**
     * Check if extractor supports the given asset
     *
     * @param FileInterface $file
     *
     * @return boolean
     */
    public function supports(FileInterface $file);

    /**
     * Extract video from file
     *
     * @param FileInterface $file
     *
     * @return string
     */
    public function extract(FileInterface $file);
}
