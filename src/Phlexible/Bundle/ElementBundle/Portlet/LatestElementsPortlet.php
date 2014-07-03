<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Latest elements portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LatestElementsPortlet extends Portlet
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var Makeweb_Elements_Element_Version_Manager
     */
    private $versionManager;

    /**
     * @var Makeweb_Elements_Tree_Manager
     */
    private $treeManager;

    /**
     * @var int
     */
    private $numItems;

    /**
     * @param TranslatorInterface                      $translator
     * @param Makeweb_Elements_Element_Version_Manager $versionManager
     * @param Makeweb_Elements_Tree_Manager            $treeManager
     * @param MWF_Db_Pool                              $dbPool
     * @param int                                      $numItems
     */
    public function __construct(
        TranslatorInterface $translator,
        Makeweb_Elements_Element_Version_Manager $versionManager,
        Makeweb_Elements_Tree_Manager $treeManager,
        MWF_Db_Pool $dbPool,
        $numItems)
    {
        $this
            ->setId('elements-portlet')
            ->setTitle($translator->trans('elements.latest_element_changes', array(), 'gui'))
            ->setClass('Makeweb.elements.portlet.LatestElements')
            ->setIconClass('p-element-component-icon')
            ->setResource('elements');

        $this->versionManager = $versionManager;
        $this->treeManager = $treeManager;
        $this->db = $dbPool->default;
        $this->numItems = $numItems;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        $select = $this->db->select()
            ->from(array('ev' => $this->db->prefix . 'element_version'), array('eid', 'trigger_language AS language'))
            ->join(
                array('e' => $this->db->prefix . 'element'),
                'ev.eid = e.eid AND ev.version = e.latest_version',
                'latest_version AS version'
            )
            ->join(array('et' => $this->db->prefix . 'element_tree'), 'ev.eid = et.eid', array('id'))
            ->order('ev.create_time DESC')
            ->limit($this->numItems);

        $items = $this->db->fetchAll($select);

        $elementVersionManager = Makeweb_Elements_Element_Version_Manager::getInstance();
        $treeManager = Makeweb_Elements_Tree_Manager::getInstance();

        $data = array();

        foreach ($items as $item) {
            $elementVersion = $elementVersionManager->get($item['eid'], $item['version']);
            $node = $treeManager->getNodeByNodeId($item['id']);
            $siteroot = $node->getTree()->getSiteroot();

            $baseTitle = $elementVersion->getBackendTitle(MWF_Env::getUser()->getInterfaceLanguage());
            $baseTitleArr = str_split($baseTitle, 16);
            $title = '';

            $first = true;
            foreach ($baseTitleArr as $chunk) {
                $title .= ($first ? '<wbr />' : '') . $chunk;
                $first = false;
            }

            $title .= ' [' . $item['id'] . ']';
            /*
                        $i = 0;
                        do
                        {
                            $title .= ($i ? '<wbr />' : '') . substr($baseTitle, $i, $i + 16);
                            $i += 16;
                        }
                        while($i <= strlen($baseTitle));
            */

            $menuItem = new MWF_Core_Menu_Item_Panel();
            $menuItem->setIdentifier('Makeweb_elements_MainPanel_' . $siteroot->getTitle())
                ->setText($siteroot->getTitle())
                ->setIconClass('p-element-component-icon')
                ->setPanel('Makeweb.elements.MainPanel')
                ->setParam('siteroot_id', $siteroot->getId())
                ->setParam('title', $siteroot->getTitle())
                ->setParam('id', $node->getId())
                ->setParam('start_tid_path', '/' . implode('/', $node->getPath()))
                ->setCheck(array('elements'));

            $menu = $menuItem->get();

            $data[] = array(
                'ident'    => $item['eid'] . '_' .
                    'de' . '_' .
                    $item['version'],
                'eid'      => $item['eid'],
                'language' => $item['language'],
                'version'  => $item['version'],
                'title'    => strip_tags($title),
                'icon'     => $elementVersion->getIconUrl(),
                'time'     => strtotime($elementVersion->getCreateTime()),
                'author'   => $elementVersion->getCreateUser()->getUsername(),
                'menu'     => $menu
            );
        }

        return $data;
    }
}