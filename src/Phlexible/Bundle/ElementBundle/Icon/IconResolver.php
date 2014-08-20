<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Icon;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TreeBundle\Model\StateManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Icon resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconResolver
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var StateManagerInterface
     */
    private $stateManager;

    /**
     * @param RouterInterface        $router
     * @param ElementService         $elementService
     * @param StateManagerInterface  $stateManager
     */
    public function __construct(
        RouterInterface $router,
        ElementService $elementService,
        StateManagerInterface $stateManager)
    {
        $this->router = $router;
        $this->elementService = $elementService;
        $this->stateManager = $stateManager;
    }

    /**
     * Resolve icon
     *
     * @param string $icon
     *
     * @return string
     */
    public function resolveIcon($icon)
    {
        return '/bundles/phlexibleelementtype/elementtypes/' . $icon;
    }

    /**
     * Resolve element type to icon
     *
     * @param Elementtype $elementtype
     *
     * @return string
     */
    public function resolveElementtype(Elementtype $elementtype)
    {
        $icon = $elementtype->getIcon();

        return $this->resolveIcon($icon);
    }

    /**
     * Resolve element to icon
     *
     * @param Element $element
     *
     * @return string
     */
    public function resolveElement(Element $element)
    {
        $elementtype = $this->elementService->findElementtype($element);

        return $this->resolveElementtype($elementtype);
    }

    /**
     * Resolve tree node to icon
     *
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     *
     * @return string
     */
    public function resolveTreeNode(TreeNodeInterface $treeNode, $language)
    {
        $parameters = array();

        if (!$treeNode->isRoot()) {
            $tree = $treeNode->getTree();

            if ($this->stateManager->isPublished($treeNode, $language)) {
                $parameters['status'] = $this->stateManager->isAsync($treeNode, $language) ? 'async': 'online';
            }

            if ($tree->isInstance($treeNode)) {
                $parameters['instance'] = $tree->isInstanceMaster($treeNode) ? 'master' : 'slave';
            }

            if ($treeNode->getSortMode() !== TreeInterface::SORT_MODE_FREE) {
                $parameters['sort'] = $treeNode->getSortMode() . '_' . $treeNode->getSortDir();
            }
        }

        $element = $this->elementService->findElement($treeNode->getTypeId());

        if (!count($parameters)) {
            return $this->resolveElement($element);
        }

        $elementtype = $this->elementService->findElementtype($element);

        $parameters['icon'] = $elementtype->getIcon();

        return $this->router->generate('elements_icon', $parameters);

        //        $uid = MWF_Env::getUid();
        //        $service = $this->getContainer()->get('locks.service');
        //        $lockIdentifier = new Makeweb_Elements_Element_Identifier($this->_eid);
        //
        //        if ($service->isLockedByUser($lockIdentifier, $uid))
        //        {
        //            $icon .= '/lock/me';
        //        }
        //        elseif ($service->isLocked($lockIdentifier))
        //        {
        //            $icon .= '/lock/other';
        //        }
    }

    /**
     * Resolve teaser to icon
     *
     * @param Teaser $teaser
     * @param string $language
     *
     * @return string
     */
    public function resolveTeaser(Teaser $teaser, $language)
    {
        $parameters = array();

        // TODO: repair
        /*
        if ($this->stateManager->isPublished($treeNode, $language)) {
            $parameters['status'] = $this->stateManager->isAsync($treeNode, $language) ? 'async': 'online';
        }

        if ($teaser->isInstance()) {
            $parameters['instance'] = $teaser->isInstanceMaster() ? 'master' : 'slave';
        }
        */

        $element = $this->elementService->findElement($teaser->getTypeId());

        if (!count($parameters)) {
            return $this->resolveElement($element);
        }

        $elementtype = $this->elementService->findElementtype($element);

        $parameters['icon'] = $elementtype->getIcon();

        return $this->router->generate('elements_icon', $parameters);

        //        $uid = MWF_Env::getUid();
        //        $service = $this->getContainer()->get('locks.service');
        //        $lockIdentifier = new Makeweb_Elements_Element_Identifier($this->_eid);
        //
        //        if ($service->isLockedByUser($lockIdentifier, $uid))
        //        {
        //            $icon .= '/lock/me';
        //        }
        //        elseif ($service->isLocked($lockIdentifier))
        //        {
        //            $icon .= '/lock/other';
        //        }
    }
}
