<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\Compiler;

use Phlexible\Component\MediaType\Model\MediaTypeCollection;

/**
 * Compiler interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CompilerInterface
{
    /**
     * @return string
     */
    public function getClassname();

    /**
     * @param MediaTypeCollection $mediaType
     *
     * @return string
     */
    public function compile(MediaTypeCollection $mediaType);
}
