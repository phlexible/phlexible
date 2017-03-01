<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Mediator;

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;

/**
 * Teaser mediator.
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
    public function accept(Teaser $teaser)
    {
        return $this->findMediator($teaser) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(Teaser $teaser, $field, $language)
    {
        if ($mediator = $this->findMediator($teaser)) {
            return $mediator->getTitle($teaser, $field, $language);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getUniqueId(Teaser $teaser)
    {
        if ($mediator = $this->findMediator($teaser)) {
            return $mediator->getUniqueId($teaser);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return Element
     */
    public function getObject(Teaser $teaser)
    {
        if ($mediator = $this->findMediator($teaser)) {
            return $mediator->getObject($teaser);
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @return ElementVersion
     */
    public function getVersionedObject(Teaser $teaser)
    {
        if ($mediator = $this->findMediator($teaser)) {
            return $mediator->getVersionedObject($teaser);
        }

        return null;
    }

    /**
     * @param Teaser $teaser
     *
     * @return MediatorInterface|null
     */
    private function findMediator(Teaser $teaser)
    {
        foreach ($this->mediators as $mediator) {
            if ($mediator->accept($teaser)) {
                return $mediator;
            }
        }

        return null;
    }
}
