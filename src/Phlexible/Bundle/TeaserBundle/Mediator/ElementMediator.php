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

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;

/**
 * Element mediator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementMediator implements MediatorInterface
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param ElementService $elementService
     */
    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(Teaser $teaser)
    {
        return $teaser->getType() === 'element';
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle(Teaser $teaser, $field, $language)
    {
        $elementVersion = $this->getVersionedObject($teaser);

        return $elementVersion->getMappedField($field, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getUniqueId(Teaser $teaser)
    {
       return $this->elementService->findElementtype($this->getObject($teaser))->getUniqueId();
    }

    /**
     * {@inheritdoc}
     *
     * @return Element
     */
    public function getObject(Teaser $teaser)
    {
        return $this->elementService->findElement($teaser->getTypeId());
    }

    /**
     * {@inheritdoc}
     *
     * @return ElementVersion
     */
    public function getVersionedObject(Teaser $teaser)
    {
        return $this->elementService->findLatestElementVersion($this->getObject($teaser));
    }
}
