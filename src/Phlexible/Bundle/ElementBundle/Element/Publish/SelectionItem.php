<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Element\Publish;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Selection item
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SelectionItem
{
    /**
     * @var TreeNodeInterface|Teaser
     */
    private $target;

    /**
     * @var int
     */
    private $version;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $title;

    /**
     * @var bool
     */
    private $isInstance;

    /**
     * @var int
     */
    private $depth;

    /**
     * @var string
     */
    private $path;

    /**
     * @param TreeNodeInterface|Teaser $target
     * @param int                      $version
     * @param string                   $language
     * @param string                   $title
     * @param bool                     $isInstance
     * @param int                      $depth
     * @param string                   $path
     */
    public function __construct($target, $version, $language, $title, $isInstance, $depth, $path)
    {
        $this->target = $target;
        $this->version = $version;
        $this->language = $language;
        $this->title = $title;
        $this->isInstance = $isInstance;
        $this->depth = $depth;
        $this->path = $path;
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @return boolean
     */
    public function isInstance()
    {
        return $this->isInstance;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return TreeNodeInterface|Teaser
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

}
