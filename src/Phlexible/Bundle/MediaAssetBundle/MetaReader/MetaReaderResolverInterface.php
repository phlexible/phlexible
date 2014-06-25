<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\MetaReader;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Meta reader resolver interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MetaReaderResolverInterface
{
    /**
     * Resolve meta reader for asset
     *
     * @param FileInterface $file
     *
     * @return MetaReaderInterface
     */
    public function resolve(FileInterface $file);
}