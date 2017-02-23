<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\File\Loader;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Loader interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface LoaderInterface
{
    /**
     * Return supported extension.
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
