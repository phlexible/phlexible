<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ContentExtractor;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

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
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return ContentExtractorInterface
     */
    public function resolve(ExtendedFileInterface $file, MediaType $mediaType);
}
