<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Element mediator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Mediator implements MediatorInterface
{
    /**
     * @var MediatorInterface[]
     */
    private $mediators = [];

    /**
     * @param MediatorInterface[] $mediators
     */
    public function __construct(array $mediators = [])
    {
        foreach ($mediators as $mediator) {
            $this->addMediator($mediator);
        }
    }

    /**
     * @param MediatorInterface $mediator
     *
     * @return $this
     */
    public function addMediator(MediatorInterface $mediator)
    {
        $this->mediators[] = $mediator;

        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function accept(TreeNodeInterface $node)
    {
        return $this->findMediator($node) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getField(TreeNodeInterface $node, $field, $language)
    {
        if ($mediator = $this->findMediator($node)) {
            return $mediator->getField($node, $field, $language);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentDocument(TreeNodeInterface $node)
    {
        if ($mediator = $this->findMediator($node)) {
            return $mediator->getContentDocument($node);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(TreeNodeInterface $node, $language)
    {
        if ($mediator = $this->findMediator($node)) {
            return $mediator->isViewable($node, $language);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isSluggable(TreeNodeInterface $node, $language)
    {
        if ($mediator = $this->findMediator($node)) {
            return $mediator->isSluggable($node, $language);
        }

        return false;
    }

    /**
     * @param TreeNodeInterface $node
     *
     * @return MediatorInterface|null
     */
    private function findMediator(TreeNodeInterface $node)
    {
        foreach ($this->mediators as $mediator) {
            if ($mediator->accept($node)) {
                return $mediator;
            }
        }

        return null;
    }
}
