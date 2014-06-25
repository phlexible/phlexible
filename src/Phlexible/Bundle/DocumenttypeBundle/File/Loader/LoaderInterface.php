<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\File\Loader;

use Phlexible\Bundle\DocumenttypeBundle\Model\Documenttype;

/**
 * XML loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * @return string
     */
    public function getExtension();

    /**
     * @param string $filename
     *
     * @return Documenttype
     */
    public function load($filename);
}
