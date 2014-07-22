<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

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