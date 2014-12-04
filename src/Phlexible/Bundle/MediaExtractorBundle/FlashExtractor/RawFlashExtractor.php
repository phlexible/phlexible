<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaExtractorBundle\FlashExtractor;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;

/**
 * Raw flash extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RawFlashExtractor implements FlashExtractorInterface
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
        return $file->getMediaType() === 'swf';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(ExtendedFileInterface $file)
    {
        return $file->getPhysicalPath();
    }
}
