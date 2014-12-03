<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\Model\AttributeBag;
use Phlexible\Component\Volume\FileSource\PathSourceInterface;

/**
 * Attribute reader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface AttributeReaderInterface
{
    /**
     * Check if requirements for reader are given
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Check if reader supports the given asset
     *
     * @param PathSourceInterface $fileSource
     * @param string              $documenttype
     * @param string              $assettype
     *
     * @return bool
     */
    public function supports(PathSourceInterface $fileSource, $documenttype, $assettype);

    /**
     * Read attributes
     *
     * @param PathSourceInterface $fileSource
     * @param string              $documenttype
     * @param string              $assettype
     * @param \Phlexible\Bundle\MediaAssetBundle\Model\AttributeBag        $attributes
     */
    public function read(PathSourceInterface $fileSource, $documenttype, $assettype, AttributeBag $attributes);

}
