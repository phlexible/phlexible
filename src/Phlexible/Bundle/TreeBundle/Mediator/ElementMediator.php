<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
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
     * @param ElementService         $elementService
     * @param ViewableVoterInterface $viewableVoter
     */
    public function __construct(ElementService $elementService, ViewableVoterInterface $viewableVoter)
    {
        $this->elementService = $elementService;
        $this->viewableVoter = $viewableVoter;
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
}
