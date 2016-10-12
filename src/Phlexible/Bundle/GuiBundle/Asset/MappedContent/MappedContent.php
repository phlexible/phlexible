<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\MappedContent;

/**
 * Mapped content
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MappedContent
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $map;

    /**
     * @param string $content
     * @param string $map
     */
    public function __construct($content, $map)
    {
        $this->content = $content;
        $this->map = $map;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return string
     */
    public function getMap()
    {
        return $this->map;
    }
}
