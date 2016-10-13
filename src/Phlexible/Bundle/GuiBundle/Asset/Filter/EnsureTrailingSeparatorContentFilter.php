<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Filter;

/**
 * Ensure trailing separator filter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class EnsureTrailingSeparatorContentFilter implements ContentFilterInterface
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
        return rtrim($content, $this->separator).$this->separator;
    }
}
