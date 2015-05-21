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
    public function getTemplate(TreeNodeInterface $node)
    {
        if ($template = $node->getTemplate()) {
            return $template;
        }

        $element = $this->elementService->findElement($node->getTypeId());
        $elementSource = $this->elementService->findElementSource($element->getElementtypeId());

        $template = $elementSource->getTemplate();

        if ($template) {
            return $template;
        }

        return '::' . $elementSource->getName() . '.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(TreeNodeInterface $node)
    {
        return $node->getType() === 'element-full';
    }
}
