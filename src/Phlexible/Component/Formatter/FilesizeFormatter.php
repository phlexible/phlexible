<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Formatter;

/**
 * Filesize formatter
 *
 * @author Stephan Wentz <swentz@brainbits.net>
 */
class FilesizeFormatter
{
    /**
     * Format a human readable filesize
     *
     * @param integer $size
     * @param integer $decimals
     * @param boolean $binarySuffix
     *
     * @return string
     */
    public function formatFilesize($size, $decimals = 0, $binarySuffix = false)
    {
        $size = (float) ceil($size);

        if (!$size) {
            return 0;
        }

        if (!$binarySuffix) {
            $divisor = 1000;
            $suffixes = array('Byte', 'kB', 'MB', 'GB', 'TB', 'PB');
        } else {
            $divisor = 1024;
            $suffixes = array('Byte', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
        }

        $last = end($suffixes);

        foreach ($suffixes as $suffix) {
            if ($size < $divisor) {
                break;
            }

            if ($suffix === $last) {
                break;
            }

            $size /= $divisor;
        }

        if ($decimals && $suffix !== $suffixes[0]) {
            $locale = localeconv();
            $decPoint = $locale['decimal_point'];
            $thousandsSep = $locale['thousands_sep'];
            $size = number_format($size, $decimals, $decPoint, $thousandsSep);
        } else {
            $size = ceil($size);
        }

        return sprintf("%s %s", $size, $suffix);
    }
}
