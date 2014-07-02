<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\ContentExtractor;

use Phlexible\Bundle\MediaAssetBundle\Content;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Content extractor interface
 *
 * @author Phillip Look <plook@brainbits.net>
 */
interface ContentExtractorInterface
{
    /**
     * Check if requirements for content reader are given
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Check if reader supports the given asset
     *
     * @param FileInterface $file
     *
     * @return bool
     */
    public function supports(FileInterface $file);

    /**
     * Extract content from asset
     *
     * @param FileInterface $file
     *
     * @return Content
     */
    public function extract(FileInterface $file);
}