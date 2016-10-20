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

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

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
     * @var ViewableVoterInterface
     */
    private $viewableVoter;

    /**
     * @var SluggableVoterInterface
     */
    private $sluggableVoter;

    /**
     * @param ElementService          $elementService
     * @param ViewableVoterInterface  $viewableVoter
     * @param SluggableVoterInterface $sluggableVoter
     */
    public function __construct(
        ElementService $elementService,
        ViewableVoterInterface $viewableVoter,
        SluggableVoterInterface $sluggableVoter
    ) {
        $this->elementService = $elementService;
        $this->viewableVoter = $viewableVoter;
        $this->sluggableVoter = $sluggableVoter;
    }

    /**
     * {@inheritdoc}
     */
    public function accept(TreeNodeInterface $node)
    {
        return $node->getType() === 'element-full' || $node->getType() === 'element-structure';
    }

    /**
     * {@inheritdoc}
     */
    public function getField(TreeNodeInterface $node, $field, $language)
    {
        $elementVersion = $this->getContentDocument($node);

        return $elementVersion->getMappedField($field, $language);
    }

    /**
     * {@inheritdoc}
     *
     * @return ElementVersion
     */
    public function getContentDocument(TreeNodeInterface $node)
    {
        return $this->elementService->findLatestElementVersion($this->elementService->findElement($node->getTypeId()));
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(TreeNodeInterface $node, $language)
    {
        return $this->viewableVoter->isViewable($node, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function isSluggable(TreeNodeInterface $node, $language)
    {
        return $this->sluggableVoter->isSluggable($node, $language);
    }
}
