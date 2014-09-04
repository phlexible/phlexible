<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Mediator;

use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;

/**
 * Teaser mediator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Mediator implements MediatorInterface
{
    /**
     * @var MediatorInterface[]
     */
    private $mediators = array();

    /**
     * @param MediatorInterface[] $mediators
     */
    public function __construct(array $mediators = array())
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
