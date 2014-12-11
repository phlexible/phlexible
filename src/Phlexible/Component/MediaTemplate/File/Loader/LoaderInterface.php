<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\File\Loader;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

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
     * @return TemplateInterface
     */
    public function load($file);
}
