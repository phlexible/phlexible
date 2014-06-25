<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

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
     * @return boolean
     */
    public function isAvailable();

    /**
     * Check if reader supports the given asset
     *
     * @param FileInterface $file
     *
     * @return boolean
     */
    public function supports(FileInterface $file);

    /**
     * Read attributes
     *
     * @param FileInterface $file
     * @param MetaBag       $metaBag
     */
    public function read(FileInterface $file, MetaBag $metaBag);

}