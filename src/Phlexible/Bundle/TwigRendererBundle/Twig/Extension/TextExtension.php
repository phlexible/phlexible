<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Twig\Extension;

use Phlexible\Component\Formatter\AgeFormatter;
use Phlexible\Component\Formatter\FilesizeFormatter;
use Phlexible\Component\Util\StringUtil;

/**
 * Twig text extension
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
        return array(
            new \Twig_SimpleFilter('truncate_html', array($this, 'truncateHtml')),
            new \Twig_SimpleFilter('nl2p', array($this, 'nl2p')),
        );
    }

    /**
     * Truncate text preserving html tags
     *
     * @param string $str
     * @param int    $length
     * @param string $suffix
     *
     * @return string
     */
    public function truncateHtml($str, $length, $suffix = '...')
    {
        $stringUitl = new StringUtil();

        return $stringUitl->truncatePreservingTags($str, $length, $suffix);
    }

    /**
     * Convert newlines to paragraph tags
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