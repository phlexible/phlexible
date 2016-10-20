<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Element\Publish;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Selection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Selection
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @param SelectionItem $item
     *
     * @return $this
     */
    public function add(SelectionItem $item)
    {
        $this->items[$item->getTarget()->getId() . '_' . $item->getLanguage()] = $item;

        return $this;
    }

    /**
     * @param TreeNodeInterface|Teaser $target
     * @param string                   $language
     *
     * @return bool
     */
    public function has($target, $language)
    {
        $key = $target->getId() . '_' . $language;

        return isset($this->items[$key]);
    }

    /**
     * @return SelectionItem[]
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param Selection $selection
     */
    public function merge(Selection $selection)
    {
        foreach ($selection->all() as $item) {
            $this->add($item);
        }
    }
}
