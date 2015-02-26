<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaExtractor\Extractor;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Extractor resolver interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ExtractorResolverInterface
{
    /**
     * Resolve extractor
     *
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     * @param string                $targetFormat
     *
     * @return ExtractorInterface
     */
    public function resolve(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat);
}
