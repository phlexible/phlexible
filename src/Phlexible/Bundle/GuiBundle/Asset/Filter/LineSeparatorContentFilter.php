<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Filter;

/**
 * Line separator filter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LineSeparatorContentFilter implements ContentFilterInterface
{
    /**
     * @var string
     */
    private $separator;

    /**
     * @param string $separator
     */
    public function __construct($separator)
    {
        $this->separator = $separator;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($content)
    {
        return preg_replace('/\r\n|\r|\n/', $this->separator, $content);
    }
}
