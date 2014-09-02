<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Model;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Teaser manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface TeaserManagerInterface
{
    /**
     * @param int $id
     *
     * @return Teaser
     */
    public function findTeaser($id);

    /**
     * @param mixed             $layoutarea
     * @param TreeNodeInterface $treeNode
     *
     * @return Teaser[]
     */
    public function findForLayoutAreaAndTreeNode($layoutarea, TreeNodeInterface $treeNode);

    /**
     * @param mixed               $layoutarea
     * @param TreeNodeInterface[] $treeNodePath
     *
     * @return array
     */
    public function findForLayoutAreaAndTreeNodePath($layoutarea, array $treeNodePath);

    /**
     * Create teaser
     *
     * @param int    $treeId
     * @param int    $eid
     * @param string $layoutareaId
     * @param string $type
     * @param int    $typeId
     * @param int    $prevId
     * @param bool   $inherit
     * @param bool   $noDisplay
     * @param string $masterLanguage
     * @param string $userId
     *
     * @return Teaser
     */
    public function createTeaser(
        $treeId,
        $eid,
        $layoutareaId,
        $type,
        $typeId,
        $prevId = 0,
        $inherit = true,
        $noDisplay = false,
        $masterLanguage = 'en',
        $userId);

    /**
     * Create teaser instance
     *
     * @param int $treeId
     * @param int $teaserId
     * @param int $layoutAreaId
     *
     * @return Teaser
     */
    public function createTeaserInstance($treeId, $teaserId, $layoutAreaId);

    /**
     * Delete teaser
     *
     * @param int $teaserId
     */
    public function deleteTeaser($teaserId);
}