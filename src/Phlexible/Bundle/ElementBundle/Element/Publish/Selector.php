<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\Element\Publish;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Model\TeaserManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Selector.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Selector
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var TeaserManagerInterface
     */
    private $teaserManager;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @param ElementService                $elementService
     * @param ElementtypeService            $elementtypeService
     * @param TreeManager                   $treeManager
     * @param TeaserManagerInterface        $teaserManager
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        ElementService $elementService,
        ElementtypeService $elementtypeService,
        TreeManager $treeManager,
        TeaserManagerInterface $teaserManager,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->elementService = $elementService;
        $this->elementtypeService = $elementtypeService;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param int    $treeId
     * @param string $language
     * @param int    $version
     * @param bool   $includeElements
     * @param bool   $includeElementInstances
     * @param bool   $includeTeasers
     * @param bool   $includeTeaserInstances
     * @param bool   $recursive
     * @param bool   $onlyOffline
     * @param bool   $onlyAsync
     *
     * @return array
     */
    public function select(
        $treeId,
        $language,
        $version,
        $includeElements,
        $includeElementInstances,
        $includeTeasers,
        $includeTeaserInstances,
        $recursive,
        $onlyOffline,
        $onlyAsync)
    {
        $tree = $this->treeManager->getByNodeId($treeId);
        $treeNode = $tree->get($treeId);

        $selection = new Selection();

        if ($includeElements) {
            $this->handleTreeNode(
                $selection,
                0,
                implode('/', $tree->getIdPath($treeNode)),
                $treeNode,
                $version,
                $language,
                $onlyAsync,
                $onlyOffline
            );
        }
        if ($includeTeasers) {
            $this->handleTreeNodeTeasers(
                $selection,
                0,
                implode('/', $tree->getIdPath($treeNode)),
                $treeNode,
                $language,
                $onlyAsync,
                $onlyOffline,
                $includeTeaserInstances
            );
        }

        if ($recursive) {
            $rii = new \RecursiveIteratorIterator($treeNode->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

            foreach ($rii as $childNode) {
                /* @var $childNode TreeNodeInterface */

                set_time_limit(5);

                if ($includeElements) {
                    $this->handleTreeNode(
                        $selection,
                        $rii->getDepth() + 1,
                        implode('/', $tree->getIdPath($childNode)),
                        $childNode,
                        null,
                        $language,
                        $onlyAsync,
                        $onlyOffline
                    );
                }
                if ($includeTeasers) {
                    $this->handleTreeNodeTeasers(
                        $selection,
                        $rii->getDepth() + 1,
                        implode('/', $tree->getIdPath($childNode)),
                        $childNode,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        $includeTeaserInstances
                    );
                }
            }
        }

        foreach ($selection->all() as $selectionItem) {
            if ($includeElementInstances && $selectionItem->getTarget() instanceof TreeNodeInterface) {
                $instanceNodes = $this->treeManager->getInstanceNodes($selectionItem->getTarget());

                foreach ($instanceNodes as $instanceNode) {
                    /* @var $instanceNode TreeNodeInterface */

                    $this->handleTreeNode(
                        $selection,
                        $selectionItem->getDepth(),
                        $selectionItem->getDepth(),
                        $instanceNode,
                        null,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        true
                    );
                }
            } elseif ($includeTeaserInstances && $selectionItem->getTarget() instanceof Teaser) {
                $instanceTeasers = $this->teaserManager->getInstances($selectionItem->getTarget());

                foreach ($instanceTeasers as $instanceTeaser) {
                    $this->handleTeaser(
                        $selection,
                        $selectionItem->getDepth(),
                        $instanceTeaser,
                        $language,
                        $onlyAsync,
                        $onlyOffline,
                        true
                    );
                }
            }
        }

        return $selection;
    }

    /**
     * @param Selection         $selection
     * @param int               $depth
     * @param array             $path
     * @param TreeNodeInterface $treeNode
     * @param int               $version
     * @param string            $language
     * @param bool              $onlyAsync
     * @param bool              $onlyOffline
     * @param bool              $isInstance
     */
    private function handleTreeNode(
        Selection $selection,
        $depth,
        $path,
        TreeNodeInterface $treeNode,
        $version,
        $language,
        $onlyAsync,
        $onlyOffline,
        $isInstance = false)
    {
        if ($selection->has($treeNode, $language)) {
            return;
        }

        $include = true;

        if ($onlyAsync || $onlyOffline) {
            $include = false;

            if ($onlyAsync && $treeNode->getTree()->isAsync($treeNode, $language)) {
                $include = true;
            }
            if ($onlyOffline && !$treeNode->getTree()->isPublished($treeNode, $language)) {
                $include = true;
            }
        }
        if (!$this->authorizationChecker->isGranted(['permission' => 'PUBLISH', 'language' => $language], $treeNode)) {
            $include = false;
        }

        if (!$include) {
            return;
        }

        $element = $this->elementService->findElement($treeNode->getTypeId());
        if ($version) {
            $elementVersion = $this->elementService->findElementVersion($element, $version);
        } else {
            $elementVersion = $this->elementService->findLatestElementVersion($element);
        }

        $selection->add(
            new SelectionItem(
                $treeNode,
                $elementVersion->getVersion(),
                $language,
                $elementVersion->getBackendTitle($language),
                $isInstance,
                $depth,
                $path.'+'.$language.'+'.$treeNode->getId().'+'.$language
            )
        );
    }

    /**
     * @param Selection         $selection
     * @param int               $depth
     * @param array             $path
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     * @param bool              $onlyAsync
     * @param bool              $onlyOffline
     * @param bool              $includeTeaserInstances
     */
    private function handleTreeNodeTeasers(
        Selection $selection,
        $depth,
        $path,
        TreeNodeInterface $treeNode,
        $language,
        $onlyAsync,
        $onlyOffline,
        $includeTeaserInstances)
    {
        $element = $this->elementService->findElement($treeNode->getTypeId());
        $elementtype = $this->elementService->findElementtype($element);

        $layoutareas = [];
        // TODO: repair
        foreach ($this->elementService->findElementtypeByType('layout') as $layoutarea) {
            if (in_array($elementtype, $this->elementService->findAllowedParents($layoutarea))) {
                $layoutareas[] = $layoutarea;
            }
        }

        foreach ($layoutareas as $layoutarea) {
            $teasers = $this->teaserManager->findForLayoutAreaAndTreeNode($layoutarea, $treeNode);

            foreach ($teasers as $teaser) {
                $this->handleTeaser($selection, $depth + 1, $path, $teaser, $language, $onlyAsync, $onlyOffline);
            }
        }
    }

    /**
     * @param Selection $selection
     * @param int       $depth
     * @param array     $path
     * @param Teaser    $teaser
     * @param string    $language
     * @param bool      $onlyAsync
     * @param bool      $onlyOffline
     * @param bool      $isInstance
     */
    private function handleTeaser(
        Selection $selection,
        $depth,
        $path,
        Teaser $teaser,
        $language,
        $onlyAsync,
        $onlyOffline,
        $isInstance = false)
    {
        if ('element' !== $teaser->getType()) {
            return;
        }

        if ($selection->has($teaser, $language)) {
            return;
        }

        $isAsync = $this->teaserManager->isAsync($teaser, $language);
        $isPublished = $this->teaserManager->isPublished($teaser, $language);

        $include = true;
        if ($onlyAsync || $onlyOffline) {
            $include = false;

            if ($onlyAsync && $isAsync) {
                $include = true;
            }
            if ($onlyOffline && !$isPublished) {
                $include = true;
            }
        }

        if (!$include) {
            return;
        }

        $element = $this->elementService->findElement($teaser->getTypeId());
        $elementVersion = $this->elementService->findLatestElementVersion($element);

        $selection->add(
            new SelectionItem(
                $teaser,
                $elementVersion->getVersion(),
                $language,
                $elementVersion->getBackendTitle($language),
                $isInstance,
                $depth,
                $path.'+'.$language.'+'.$teaser->getId().'+'.$language
            )
        );
    }
}
