<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle;

/**
 * Asset content
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Content
{
    /**
     * @var string
     */
    private $content = '';

    /**
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content = (string) $content;
    }

    /**
     * Set new content.
     *
     * @param string $content
     */
    public function set($content)
    {
        $this->content = (string) $content;
    }

    /**
     * Get content as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->content;
    }

}