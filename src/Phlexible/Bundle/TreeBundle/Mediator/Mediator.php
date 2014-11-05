<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
    public function getTitle(TreeNodeInterface $node, $field, $language)
    {
        if ($mediator = $this->findMediator($node)) {
            return $mediator->getTitle($node, $field, $language);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getObject(TreeNodeInterface $node)
    {
        if ($mediator = $this->findMediator($node)) {
            return $mediator->getObject($node);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionedObject(TreeNodeInterface $node)
    {
        if ($mediator = $this->findMediator($node)) {
            return $mediator->getVersionedObject($node);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(TreeNodeInterface $node)
    {
        if ($mediator = $this->findMediator($node)) {
            return $mediator->isViewable($node);
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
