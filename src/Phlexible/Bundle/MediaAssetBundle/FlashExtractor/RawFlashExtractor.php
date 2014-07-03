<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\FlashExtractor;

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
    public function supports(Asset $asset)
    {
        return $asset->getDocumenttype()->getKey() === 'swf';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Asset $asset)
    {
        return $asset->getFilename();
    }
}
