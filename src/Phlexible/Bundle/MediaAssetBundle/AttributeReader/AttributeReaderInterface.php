<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\AttributesBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;

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
     * @param FileInterface       $file
     * @param PathSourceInterface $fileSource
     *
     * @return bool
     */
    public function supports(FileInterface $file, PathSourceInterface $fileSource);

    /**
     * Read attributes
     *
     * @param FileInterface       $file
     * @param PathSourceInterface $fileSource
     * @param AttributesBag       $attributes
     */
    public function read(FileInterface $file, PathSourceInterface $fileSource, AttributesBag $attributes);

}