<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Set node offline event.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SetNodeOfflineEvent extends NodeEvent
{
    /**
     * @var string
     */
    private $language;

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     */
    public function __construct(TreeNodeInterface $node, $language)
    {
        parent::__construct($node);

        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
