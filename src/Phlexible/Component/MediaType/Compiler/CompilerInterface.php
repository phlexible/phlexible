<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\Compiler;

use Phlexible\Component\MediaType\Model\MediaTypeCollection;

/**
 * Compiler interface.
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
