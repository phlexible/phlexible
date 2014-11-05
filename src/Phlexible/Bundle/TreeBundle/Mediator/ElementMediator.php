<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
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
     * @param ElementService $elementService
     */
    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
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
    public function getTitle(TreeNodeInterface $node, $field, $language)
    {
        $elementVersion = $this->getVersionedObject($node);

        return $elementVersion->getMappedField($field, $language);
    }

    /**
     * {@inheritdoc}
     *
     * @return Element
     */
    public function getObject(TreeNodeInterface $node)
    {
        return $this->elementService->findElement($node->getTypeId());
    }

    /**
     * {@inheritdoc}
     *
     * @return ElementVersion
     */
    public function getVersionedObject(TreeNodeInterface $node)
    {
        return $this->elementService->findLatestElementVersion($this->getObject($node));
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(TreeNodeInterface $node)
    {
        return $node->getType() === 'element-full';
    }
}
