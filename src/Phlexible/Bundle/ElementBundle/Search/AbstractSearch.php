<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Search;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;

/**
 * Abstract element search
 *
 * @author  Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractSearch implements SearchProviderInterface
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $db;

    /**
     * @var string
     */
    protected $defaultLanguage;

    /**
     * @param ConnectionManager $dbPool
     * @param string            $defaultLanguage
     */
    public function __construct(ConnectionManager $dbPool, $defaultLanguage)
    {
        $this->db = $dbPool->default;
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return 'elements';
    }

    /**
     * Perform search
     *
     * @param \Zend_Db_Select $select
     * @param string         $title
     * @param string         $language
     * @return array
     */
    protected function _doSearch(\Zend_Db_Select $select, $title, $language = null)
    {
        if ($language === null)
        {
            $language = $this->defaultLanguage;
        }

        $rows = $this->db->fetchAll($select);

        $elementVersionManager     = Makeweb_Elements_Element_Version_Manager::getInstance();
        $siteRootManager           = Makeweb_Siteroots_Siteroot_Manager::getInstance();
        $treeManager               = Makeweb_Elements_Tree_Manager::getInstance();

        $rightsIdentifiers = array(
            array('uid' => MWF_Env::getUid())
        );
        foreach (MWF_Env::getUser()->getGroups() as $group)
        {
            $rightsIdentifiers[] = array('gid' => $group->getId());
        }

        $contentRightsManager = MWF_Registry::getContainer()->contentRightsManager;

        $results = array();
        foreach ($rows as $row)
        {
            $node = $treeManager->getNodeByNodeId($row['id']);

            if (!MWF_Env::getUser()->isGranted(MWF_Core_Acl_Acl::RESOURCE_SUPERADMIN) &&
                !MWF_Env::getUser()->isGranted(MWF_Core_Acl_Acl::RESOURCE_DEVELOPMENT))
            {
                $contentRightsManager->calculateRights('internal', $node, $rightsIdentifiers);

                if (true !== $contentRightsManager->hasRight('VIEW', $language))
                {
                    continue;
                }
            }

            $elementVersion = $elementVersionManager->getLatest($node->getEid(), $language);
            $siteRoot       = $siteRootManager->getByID($node->getTree()->getSiteRootId());

            $menuItem = new MWF_Core_Menu_Item_Panel();
            $menuItem->setPanel('Makeweb.elements.MainPanel')
                     ->setIdentifier('Makeweb_elements_MainPanel_' . $siteRoot->getTitle($language))
                     ->setParam('id', $node->getId())
                     ->setParam('siteroot_id', $siteRoot->getId())
                     ->setParam('title', $siteRoot->getTitle())
                     ->setParam('start_tid_path', '/' . implode('/', $node->getPath()));

            try
            {
                $createUser = MWF_Core_Users_User_Peer::getByUserID($elementVersion->getCreateUserID());
            }
            catch(Exception $e)
            {
                $createUser = MWF_Core_Users_User_Peer::getSystemUser();
            }

            $iconParams = array(
                'status'   => $node->isAsync($language) ? 'async' : ($node->isPublished($language) ? 'online' : null),
                'instance' => ($node->isInstance() ? ($node->isInstanceMaster() ? 'master' : 'slave') : false),
            );

            $results[] = new MWF_Core_Search_Result(
                $node->getId(),
                $siteRoot->getTitle($language) . ' :: ' . $elementVersion->getBackendTitle($language) . ' (' . $language . ', ' . $node->getId() .')',
                $createUser->getFirstname().' '.$createUser->getLastname(),
                strtotime($elementVersion->getCreateTime()),
                $elementVersion->getIconUrl($iconParams),
                $title,
                $menuItem
            );
        }

        return $results;
    }
}