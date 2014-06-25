<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AudioExtractor;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Raw audio extractor
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RawAudioExtractor implements AudioExtractorInterface
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
    public function supports(FileInterface $file)
    {
        return strtolower($file->getAttribute('assettype')) === 'audio';
    }

    /**
     * {@inheritdoc}
     */
    public function extract(FileInterface $file)
    {
        return $file->getPhysicalPath();
    }
}
