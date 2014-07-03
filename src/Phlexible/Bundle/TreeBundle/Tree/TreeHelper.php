<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @copyright   2010 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

namespace Phlexible\Bundle\TreeBundle;

/**
 * Tree helper
 *
 * @author Phillip Look <pl@brainbits.net>
 */
class TreeHelper
{
    /**
     * @var Makeweb_Elements_Tree_Manager
     */
    protected $_treeManager;

    /**
     * @var Makeweb_Elements_Element_Version_Manager
     */
    protected $_elementVersionManager;

    /**
     * constructor
     *
     * @param Makeweb_Elements_Tree_Manager            $treeManager
     * @param Makeweb_Elements_Element_Version_Manager $elementVersionManager
     */
    public function __construct(
        Makeweb_Elements_Tree_Manager $treeManager,
        Makeweb_Elements_Element_Version_Manager $elementVersionManager)
    {
        $this->_treeManager = $treeManager;
        $this->_elementVersionManager = $elementVersionManager;
    }

    /**
     * get data data wrap for the online element version
     *
     * @param int    $tid
     * @param string $language
     *
     * @return Makeweb_Elements_Element_Version_Data_Wrap|null if tid is not found or language is not online
     */
    public function getOnlineWrapByTid($tid, $language)
    {
        $elementVersion = $this->getOnlineVersionByTid($tid, $language);
        if (!$elementVersion) {
            return null;
        }

        $elementData = $elementVersion->getData($language);
        $wrap = $elementData->getWrap();

        return $wrap;
    }

    /**
     * get online element version
     *
     * @param int    $tid
     * @param string $language
     *
     * @return Makeweb_Elements_Element_Version|null if tid is not found or language is not online
     */
    public function getOnlineVersionByTid($tid, $language)
    {
        try {
            $node = $this->_treeManager->getNodeByNodeId($tid);
        } catch (Makeweb_Elements_Tree_Exception $e) {
            MWF_Log::exception($e);

            return null;
        }

        $elementVersion = $this->getOnlineVersionByNode($node, $language);

        return $elementVersion;
    }

    /**
     * get online element version
     *
     * @param Makeweb_Elements_Tree_Node $node
     * @param string                     $language
     *
     * @return Makeweb_Elements_Element_Version|null if language is not online
     */
    public function getOnlineVersionByNode(Makeweb_Elements_Tree_Node $node, $language)
    {
        // get element online version
        $eid = $node->getEid();
        $version = $node->getOnlineVersion($language);
        if (!$version) {
            return null;
        }

        $elementVersion = $this->_elementVersionManager->get($eid, $version);

        return $elementVersion;
    }

    /**
     * get title of online version
     *
     * @param int    $tid
     * @param string $language
     * @param string $section
     * @param string $fallbackLanguage
     *
     * @return string|null if tid is not found or language is not online
     */
    public function getOnlineTitleByTid($tid, $language, $section = 'navigation', $fallbackLanguage = null)
    {
        $elementVersion = $this->getOnlineVersionByTid($tid, $language);
        if (!$elementVersion) {
            return null;
        }

        $title = $elementVersion->getTitle($section, $language, $fallbackLanguage);

        return $title;
    }

    /**
     * get title of online version
     *
     * @param int    $tid
     * @param string $language
     * @param string $section
     * @param string $fallbackLanguage
     *
     * @return string|null if tid is not found or language is not online
     */
    public function getOnlineChildTitlesByTid($tid, $language, $section = 'navigation', $fallbackLanguage = null)
    {
        $childTitles = array();

        try {
            $node = $this->_treeManager->getNodeByNodeId($tid);
        } catch (Makeweb_Elements_Tree_Exception $e) {
            MWF_Log::exception($e);

            return array();
        }

        foreach ($node->getChildren() as $childNode) {
            /* @var $childNode Makeweb_Elements_Tree_Node */
            $childTid = $childNode->getId();

            $childTitle = $this->getOnlineTitleByTid(
                $childTid,
                $language,
                $section,
                $fallbackLanguage
            );

            if (mb_strlen($childTitle)) {
                $childTitles[$childTid] = $childTitle;
            }
        }

        asort($childTitles);

        return $childTitles;
    }

}

