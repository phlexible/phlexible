<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Finder;

use Puli\Repository\Resource\FileResource;

/**
 * Resource finder interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ResourceFinderInterface
{
    /**
     * @param string $typeName
     *
     * @return FileResource[]
     */
    public function findByType($typeName);
}
