<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MessageBundle\Handler;

/**
 * Handler collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class HandlerCollection implements \IteratorAggregate
{
    /**
     * @var HandlerInterface[]
     */
    private $handlers = array();

    /**
     * @param HandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        foreach ($handlers as $handler) {
            $this->addHandler($handler);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addHandler(HandlerInterface $handler)
    {
        $this->handlers[] = $handler;

        return $this;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->handlers);
    }
}
