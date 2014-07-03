<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Search;

use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Bundle\SearchBundle\SearchProvider\SearchProviderInterface;
use Phlexible\Component\Database\ConnectionManager;

/**
 * Meta search
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSearch implements SearchProviderInterface
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @param ConnectionManager $connectionManager
     * @param SiteManager       $siteManager
     */
    public function __construct(ConnectionManager $connectionManager, SiteManager $siteManager)
    {
        $this->db = $connectionManager->default;
        $this->siteManager = $siteManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getResource()
    {
        return 'mediamanager';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchKey()
    {
        return 'mm';
    }

    /**
     * {@inheritdoc}
     */
    public function search($query)
    {
        $site = $this->siteManager->get('mediamanager');
        $folderPeer = $site->getFolderPeer();

        $select = $this->db
            ->select()
            ->from(
                array('f' => $this->db->prefix . 'mediamanager_files'),
                array('id', 'folder_id', 'name', 'create_user_id', 'create_time')
            )
            ->join(
                array('m' => $this->db->prefix . 'mediamanager_files_metasets_items'),
                'm.file_id = f.id AND m.file_version = m.file_version',
                array()
            )
            ->where('m.meta_value_de LIKE ?', '%' . $query . '%')
            ->orWhere('m.meta_value_en LIKE ?', '%' . $query . '%')
            ->order('m.meta_key');

        $files = $this->db->fetchAll($select);

        $folders = array();
        $results = array();
        foreach ($files as $fileRow) {
            if (empty($folders[$fileRow['folder_id']])) {
                $folders[$fileRow['folder_id']] = $folderPeer->getByID($fileRow['folder_id']);
            }

            if (!$folders[$fileRow['folder_id']]->checkRight(MWF_Env::getUser(), 'FILE_READ')) {
                continue;
            }

            $folderPath = $folders[$fileRow['folder_id']]->getIdPath();

            $menuItem = new MWF_Core_Menu_Item_Panel();
            $menuItem->setPanel('Phlexible.mediamanager.MediamanagerPanel')
                ->setParam('start_file_id', $fileRow['id'])
                ->setParam('start_folder_path', $folderPath);

            try {
                $createUser = MWF_Core_Users_User_Peer::getByUserID($fileRow['create_user_id']);
            } catch (Exception $e) {
                $createUser = MWF_Core_Users_User_Peer::getSystemUser();
            }

            $results[] = new MWF_Core_Search_Result(
                $fileRow['id'],
                $fileRow['name'],
                $createUser->getFirstname() . ' ' . $createUser->getLastname(),
                strtotime($fileRow['create_time']),
                CMS_URL . '/media/' . $fileRow['id'] . '/_mm_small',
                'Mediamanager Meta Search',
                $menuItem
            );
        }

        return $results;
    }
}
