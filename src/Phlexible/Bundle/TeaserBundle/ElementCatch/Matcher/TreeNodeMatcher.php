<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ElementCatch\Matcher;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManager;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tree node matcher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeNodeMatcher
{
    /**
     * @var \Phlexible\Bundle\TreeBundle\Tree\TreeManager
     */
    private $contentTreeManager;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var bool
     */
    private $useElementLanguageAsFallback;

    /**
     * @param ContentTreeManager $contentTreeManager
     * @param ElementService     $elementService
     * @param bool               $useElementLanguageAsFallback
     */
    public function __construct(
        ContentTreeManager $contentTreeManager,
        ElementService $elementService,
        $useElementLanguageAsFallback)
    {
        $this->contentTreeManager = $contentTreeManager;
        $this->elementService = $elementService;
        $this->useElementLanguageAsFallback = $useElementLanguageAsFallback;
    }

    /**
     * Traverse tree and find matching nodes.
     * - check max depth
     *
     * @param int   $treeId
     * @param int   $maxDepth
     * @param bool  $isPreview
     * @param array $languages
     *
     * @return array
     */
    public function getMatchingTreeIdsByLanguage($treeId, $maxDepth, $isPreview, $languages)
    {
        try {
            $tree = $this->contentTreeManager->findByTreeId($treeId);
        } catch (\Exception $e) {
            \MWF_Log::warn("Missing tree node ($treeId) of catch, maybe it is deleted");

            return array('');
        }

        $typeFilter = array(
            'element'
        );

        $iterator = new TreeIterator($tree->get($treeId, $typeFilter));

        // create RecursiveIteratorIterator
        $rii = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);
        $rii->setMaxDepth($maxDepth);

        $catched = array();
        foreach ($rii as $childNode) {
            /* @var $childNode \Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface */

            if ($isPreview) {
                $onlineLanguages = Makeweb_Elements_History::getSavedLanguagesByEid(
                    $childNode->getTypeId()
                );
            } else {
                $onlineLanguages = array('de'); //$childNode->getOnlineLanguages();
            }

            $childTreeId = (int) $childNode->getId();

            foreach ($languages as $language) {
                if (in_array($language, $onlineLanguages)) {
                    if (!isset($catched[$language])) {
                        $catched[$language] = array();
                    }

                    $catched[$language][] = $childTreeId;
                    break;
                }
            }

            // if master language should be used as fallback
            // and child node was not found yet
            // -> use master language as fallback
            // TODO: problem - $language might be unset
            if ($this->useElementLanguageAsFallback && (!isset($catched[$language]) || !in_array(
                        $childTreeId,
                        $catched[$language]
                    ))
            ) {
                $masterLanguage = $this->elementService
                    ->findElement($childNode->getTypeId())
                    ->getMasterLanguage();

                // master language is published
                // and master language was not processed yet
                if (in_array($masterLanguage, $onlineLanguages) && !in_array($masterLanguage, $languages)) {
                    $catched[$masterLanguage][] = (int) $childNode->getId();
                }
            }
        }

        $matchedTreeIdsByLanguage = count($catched) ? $catched : array('');

        return $matchedTreeIdsByLanguage;
    }

    /**
     * Flatten matched tree ids by language to simple tree id array
     *
     * @param array $matchedTreeIdsByLanguage
     *
     * @return array
     */
    public function flatten(array $matchedTreeIdsByLanguage)
    {
        $matchedTreeIds = array();
        if (current($matchedTreeIdsByLanguage) !== '') {
            foreach ($matchedTreeIdsByLanguage as $treeIds) {
                $matchedTreeIds = array_merge($matchedTreeIds, $treeIds);
            }
        }

        return $matchedTreeIds;
    }
}
