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

use Phlexible\Component\Util\StringUtil;

/**
 * Twig text extension.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TextExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('truncate_html', [$this, 'truncateHtml']),
            new \Twig_SimpleFilter('nl2p', [$this, 'nl2p']),
        ];
    }

    /**
     * Truncate text preserving html tags.
     *
     * @param string $str
     * @param int    $length
     * @param string $suffix
     *
     * @return string
     */
    public function truncateHtml($str, $length, $suffix = '&hellip;')
    {
        $stringUitl = new StringUtil();

        return $stringUitl->truncatePreservingTags($str, $length, $suffix);
    }

    /**
     * Convert newlines to paragraph tags.
     *
     * @param string $str
     *
     * @return string
     */
    public function nl2p($str)
    {
        $stringUitl = new StringUtil();

        return $stringUitl->nl2p($str);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_text';
    }
}
