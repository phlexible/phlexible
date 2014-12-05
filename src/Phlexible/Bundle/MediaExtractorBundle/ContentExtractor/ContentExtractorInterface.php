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
 * Content extractor interface
 *
 * @author Phillip Look <plook@brainbits.net>
 */
interface ContentExtractorInterface
{
    /**
     * Check if reader supports the given asset
     *
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return bool
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType);

    /**
     * Extract content from asset
     *
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return Content
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType);
}
