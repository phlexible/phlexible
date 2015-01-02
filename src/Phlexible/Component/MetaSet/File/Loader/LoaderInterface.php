<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File\Loader;

use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * Loader interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * Return supported extension
     *
     * @return string
     */
    public function getExtension();

    /**
     * @param string $file
     *
     * @return MetaSetInterface
     */
    public function load($file);
}
