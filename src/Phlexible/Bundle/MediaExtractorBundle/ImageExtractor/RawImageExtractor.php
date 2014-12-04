<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\ImageExtractor;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;

/**
 * Raw image extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RawImageExtractor implements ImageExtractorInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ExtendedFileInterface $file)
    {
        die("XXX");
        return strtolower($file->getMediaType()) === 'image' || strtolower($file->getMediaType()) === 'pdf';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file)
    {
        return $file->getPhysicalPath();
    }
}
