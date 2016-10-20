<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\CmsBundle\Twig\Extension;

use Phlexible\Component\Formatter\AgeFormatter;
use Phlexible\Component\Formatter\FilesizeFormatter;

/**
 * Twig misc extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MiscExtension extends \Twig_Extension
{
    private static $id = 0;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('id', [$this, 'id']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('readable_size', [$this, 'readableSize']),
            new \Twig_SimpleFilter('age', [$this, 'age']),
        ];
    }

    /**
     * Generate and return a unique id
     *
     * @param string $prefix
     *
     * @return string
     */
    public function id($prefix = '')
    {
        // raise id
        $id = ++self::$id;

        if ($prefix) {
            $id = $prefix . $id;
        }

        return $id;
    }

    /**
     * Return readable file size for given value
     *
     * @param int  $size
     * @param int  $decimals
     * @param bool $binarySuffix
     *
     * @return string
     */
    public function readableSize($size, $decimals = 0, $binarySuffix = false)
    {
        $formatter = new FilesizeFormatter();

        return $formatter->formatFilesize($size, $decimals, $binarySuffix);
    }

    /**
     * Return age string for given date
     *
     * @param string      $date1
     * @param string|null $date2
     *
     * @return string
     */
    public function age($date1, $date2 = null)
    {
        $formatter = new AgeFormatter();

        return $formatter->formatDate($date1, $date2);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_misc';
    }
}
