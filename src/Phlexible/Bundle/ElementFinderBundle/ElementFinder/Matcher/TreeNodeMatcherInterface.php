<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder\Matcher;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Bundle\TreeBundle\Tree\TreeIterator;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Tree node matcher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TreeNodeMatcherInterface
{
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
    public function getMatchingTreeIdsByLanguage($treeId, $maxDepth, $isPreview, $languages);

    /**
     * Flatten matched tree ids by language to simple tree id array
     *
     * @param array $matchedTreeIdsByLanguage
     *
     * @return array
     */
    public function flatten(array $matchedTreeIdsByLanguage);
}
