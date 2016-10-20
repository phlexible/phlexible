<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\File\Dumper;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Dumper interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * Return supported extension
     *
     * @return string
     */
    public function getExtension();

    /**
     * @param string            $file
     * @param TemplateInterface $template
     */
    public function dump($file, TemplateInterface $template);
}
