<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaExtractor\ImageExtractor;

use Phlexible\Component\MediaExtractor\Extractor\ExtractorInterface;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaType\Model\MediaType;

/**
 * Raw image extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RawImageExtractor implements ExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return $targetFormat === 'image' && ($mediaType->getCategory() === 'image' || $mediaType->getName() === 'pdf');
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file, MediaType $mediaType, $targetFormat)
    {
        return $file->getPhysicalPath();
    }
}
