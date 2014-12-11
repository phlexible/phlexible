<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\AttributeReader;

use Phlexible\Component\MediaType\Model\MediaType;
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
     * @param MediaType           $mediaType
     *
     * @return bool
     */
    public function supports(PathSourceInterface $fileSource, MediaType $mediaType);

    /**
     * Read attributes
     *
     * @param PathSourceInterface $fileSource
     * @param MediaType           $mediaType
     * @param AttributeBag        $attributes
     */
    public function read(PathSourceInterface $fileSource, MediaType $mediaType, AttributeBag $attributes);

}
