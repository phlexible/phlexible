<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TeaserBundle\Model;

use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TeaserBundle\Entity\TeaserOnline;
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
    public function find($id);

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return Teaser[]
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param array $criteria
     *
     * @return Teaser
     */
    public function findOneBy(array $criteria);

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
     * @param bool                $includeLocalHidden
     *
     * @return Teaser[]
     */
    public function findForLayoutAreaAndTreeNodePath($layoutarea, array $treeNodePath, $includeLocalHidden = true);

    /**
     * @param Teaser $teaser
     *
     * @return bool
     */
    public function isInstance(Teaser $teaser);

    /**
     * @param Teaser $teaser
     *
     * @return bool
     */
    public function isInstanceMaster(Teaser $teaser);

    /**
     * @param Teaser $teaser
     *
     * @return Teaser[]
     */
    public function getInstances(Teaser $teaser);

    /**
     * @param Teaser $teaser
     * @param string $language
     *
     * @return bool
     */
    public function isPublished(Teaser $teaser, $language);

    /**
     * @param Teaser $teaser
     *
     * @return array
     */
    public function getPublishedLanguages(Teaser $teaser);

    /**
     * @param Teaser $teaser
     * @param string $language
     *
     * @return int
     */
    public function getPublishedVersion(Teaser $teaser, $language);

    /**
     * @param Teaser $teaser
     *
     * @return array
     */
    public function getPublishedVersions(Teaser $teaser);

    /**
     * @param Teaser $teaser
     * @param string $language
     *
     * @return bool
     */
    public function isAsync(Teaser $teaser, $language);

    /**
     * @param Teaser $teaser
     *
     * @return TeaserOnline
     */
    public function findOnlineByTeaser(Teaser $teaser);

    /**
     * @param Teaser $teaser
     * @param string $language
     *
     * @return TeaserOnline[]
     */
    public function findOneOnlineByTeaserAndLanguage(Teaser $teaser, $language);

    /**
     * Create teaser
     *
     * @param int    $treeId
     * @param int    $eid
     * @param string $layoutareaId
     * @param string $type
     * @param int    $typeId
     * @param int    $prevId
     * @param array  $stopIds
     * @param array  $hideIds
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
        array $stopIds = null,
        array $hideIds = null,
        $masterLanguage = 'en',
        $userId);

    /**
     * Create teaser instance
     *
     * @param TreeNodeInterface $treeNode
     * @param Teaser            $teaser
     * @param int               $layoutAreaId
     * @param string            $userId
     *
     * @return Teaser
     */
    public function createTeaserInstance(TreeNodeInterface $treeNode, Teaser $teaser, $layoutAreaId, $userId);

    /**
     * @param Teaser $teaser
     * @param bool   $flush
     */
    public function updateTeaser(Teaser $teaser, $flush = true);

    /**
     * @param Teaser[] $teasers
     * @param bool  $flush
     */
    public function updateTeasers(array $teasers, $flush = true);

    /**
     * @param Teaser $teaser
     * @param string $userId
     */
    public function deleteTeaser(Teaser $teaser, $userId);

    /**
     * @param Teaser      $teaser
     * @param int         $version
     * @param string      $language
     * @param string      $userId
     * @param string|null $comment
     */
    public function publishTeaser(Teaser $teaser, $version, $language, $userId, $comment = null);

    /**
     * @param Teaser      $teaser
     * @param string      $language
     * @param string      $userId
     * @param string|null $comment
     */
    public function setTeaserOffline(Teaser $teaser, $language, $userId, $comment = null);
}
