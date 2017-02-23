<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Event;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Save node data event.
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
     * @var Request
     */
    private $request;

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     * @param Request           $request
     */
    public function __construct(TreeNodeInterface $node, $language, Request $request)
    {
        $this->node = $node;
        $this->language = $language;
        $this->request = $request;
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
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
