<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\File\Dumper;

use Phlexible\Component\MetaSet\Model\MetaSetInterface;

/**
 * Dumper interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * Return supported extension.
     *
     * @return string
     */
    public function getExtension();

    /**
     * @param string           $file
     * @param MetaSetInterface $metaSet
     */
    public function dump($file, MetaSetInterface $metaSet);
}
