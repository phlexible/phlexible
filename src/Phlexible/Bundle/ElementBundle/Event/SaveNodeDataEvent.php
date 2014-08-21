<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Save node data event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SaveNodeDataEvent extends Event
{
    /**
     * @var TreeNodeInterface
     */
    private $node;

    /**
     * @var string
     */
    private $language;

    /**
     * @var array
     */
    private $data;

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     * @param array             $data
     */
    public function __construct(TreeNodeInterface $node, $language, array $data)
    {
        $this->node = $node;
        $this->language = $language;
        $this->data = $data;
    }

    /**
     * @return TreeNodeInterface
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
