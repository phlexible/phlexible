<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\MetaReader;

use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Meta reader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaReaderInterface
{
    /**
     * Check if requirements for meta reader are given
     *
     * @return bool
     */
    public function isAvailable();

    /**
     * Check if reader supports the given asset
     *
     * @param FileInterface $file
     *
     * @return bool
     */
    public function supports(FileInterface $file);

    /**
     * Read file meta
     *
     * @param FileInterface $file
     * @param MetaBag       $metaBag
     */
    public function read(FileInterface $file, MetaBag $metaBag);
}