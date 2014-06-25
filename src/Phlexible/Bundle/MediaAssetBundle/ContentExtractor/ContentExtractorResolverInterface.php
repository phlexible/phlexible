<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\ContentExtractor;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Content extractor resolver interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ContentExtractorResolverInterface
{
    /**
     * Resolve meta reader for asset
     *
     * @param FileInterface $file
     *
     * @return ContentExtractorInterface
     */
    public function resolve(FileInterface $file);
}