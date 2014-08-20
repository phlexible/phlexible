<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Model;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Phlexible\Bundle\TeaserBundle\Entity\ElementCatch;
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
     * Get Layout Area Id (Elementtype ID) by Teaser ID.
     *
     * @param int $id
     *
     * @return int teaser eid
     */
    public function getLayoutAreaIdById($id);

    /**
     * Create catch
     *
     * @param string   $treeId               Tree ID
     * @param string   $eid                  EID
     * @param string   $layoutAreaId         Layout Area ID
     *
     * @return ElementCatch
     */
    public function createCatch($treeId, $eid, $layoutAreaId);

    /**
     * Return all Teasers for the given EID
     *
     * @return array
     */
    public function getAllByEID($eid, $areaId = null, $inheritSiterootID = null);

    /**
     * Delete a cacth teaeser.
     *
     * @param int $catchId Teaser id.
     */
    public function deleteCatch($catchId);

    /**
     * Get Teaser EID by Teaser ID.
     *
     * @param int $id
     *
     * @return int
     */
    public function getTeaserEidById($id);

    /**
     * Publish a teaser.
     *
     * @param int    $teaserId
     * @param int    $version
     * @param string $language
     * @param string $comment
     * @param int    $tid
     *
     * @return int
     */
    public function publish($teaserId, $version, $language, $comment, $tid);

    /**
     * Check if teaser is inherited
     *
     * @param int $teaserId
     * @param int $tid
     *
     * @return bool
     */
    public function isInherited($teaserId, $tid);

    /**
     * Create new Element
     *
     * @param string $treeId
     * @param string $eid
     * @param string $layoutAreaId
     * @param int    $newElementTypeID
     * @param int    $prevId
     * @param bool   $inherit
     * @param bool   $noDisplay
     * @param string $masterLanguage
     *
     * @return Teaser
     *
     * @throws \Exception
     */
    public function createTeaser(
        $treeId,
        $eid,
        $layoutAreaId,
        $newElementTypeID,
        $prevId = 0,
        $inherit = true,
        $noDisplay = false,
        $masterLanguage = 'en');

    /**
     * Create new teaser instance
     *
     * @param int $treeId
     * @param int $teaserId
     * @param int $layoutAreaId
     *
     * @return Teaser
     */
    public function createTeaserInstance($treeId, $teaserId, $layoutAreaId);

    /**
     * Return all teasers on given tree path
     * Will flatten the inherited teasers
     *
     * @param array              $treePath
     * @param ElementtypeVersion $layoutArea
     * @param string             $language
     * @param array              $availableLanguages
     * @param bool               $isPreview
     *
     * @return array
     */
    public function getAllByTIDPathFlat(
        $treePath,
        ElementtypeVersion $layoutArea,
        $language = null,
        array $availableLanguages = array(),
        $isPreview = false);

    /**
     * Return a Teaser by ID
     *
     * @param string $eid
     * @param bool   $version
     *
     * @return ElementVersion
     */
    public function getByEID($eid, $version = null);

    /**
     * Delete the specified Teaser
     *
     * @param int $teaserId Teaser id.
     */
    public function deleteTeaser($teaserId);

    /**
     * Return all teasers on given tree path
     *
     * @param array              $treePath
     * @param ElementtypeVersion $layoutArea
     * @param string             $language
     * @param array              $availableLanguages
     * @param bool               $isPreview
     */
    public function getAllByTIDPath(
        $treePath,
        ElementtypeVersion $layoutArea,
        $language = null,
        array $availableLanguages = array(),
        $isPreview = false);

    /**
     * Check if teaser is published
     *
     * @param int    $eid
     * @param string $language
     *
     * @return bool
     */
    public function isPublished($eid, $language);

    public function setOffline($teaserId, $language);

    /**
     * Save catch
     *
     * @param string   $teaserId
     * @param int      $forTreeId
     * @param array    $catchElementTypeId
     * @param bool     $catchInNavigation
     * @param int      $catchMaxDepth
     * @param string   $catchSortField
     * @param string   $catchSortOrder
     * @param callback $catchFilter
     * @param bool     $catchPaginator
     * @param int      $catchMaxElements
     * @param bool     $catchRotation
     * @param int      $catchPoolSize
     * @param int      $catchElementsPerPage
     * @param string   $catchTemplate
     * @param array    $catchMetaSearch
     *
     * @return ElementCatch
     */
    public function saveCatch(
        $teaserId,
        $forTreeId,
        array $catchElementTypeId,
        $catchInNavigation,
        $catchMaxDepth,
        $catchSortField,
        $catchSortOrder,
        $catchFilter,
        $catchPaginator,
        $catchMaxElements,
        $catchRotation,
        $catchPoolSize,
        $catchElementsPerPage,
        $catchTemplate,
        array $catchMetaSearch);

    /**
     * @param       $tid
     * @param null  $areaId
     * @param null  $language
     * @param bool  $includeInherit
     * @param array $availableLanguages
     * @param bool  $isPreview
     *
     * @return mixed
     */
    public function getAllByTID(
        $tid,
        $areaId = null,
        $language = null,
        $includeInherit = false,
        array $availableLanguages = array(),
        $isPreview = false);
}