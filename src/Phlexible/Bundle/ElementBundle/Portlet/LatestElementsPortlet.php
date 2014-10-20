<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Portlet;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Icon\IconResolver;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Latest elements portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LatestElementsPortlet extends Portlet
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var IconResolver
     */
    private $iconResolver;

    /**
     * @var int
     */
    private $numItems;

    /**
     * @param TranslatorInterface $translator
     * @param ElementService      $elementService
     * @param TreeManager         $treeManager
     * @param IconResolver        $iconResolver
     * @param Connection          $connection
     * @param int                 $numItems
     */
    public function __construct(
        TranslatorInterface $translator,
        ElementService $elementService,
        TreeManager $treeManager,
        IconResolver $iconResolver,
        Connection $connection,
        $numItems)
    {
        $this
            ->setId('elements-portlet')
            ->setTitle($translator->trans('elements.latest_element_changes', [], 'gui'))
            ->setClass('Phlexible.elements.portlet.LatestElements')
            ->setIconClass('p-element-component-icon')
            ->setResource('elements');

        $this->elementService = $elementService;
        $this->treeManager = $treeManager;
        $this->iconResolver = $iconResolver;
        $this->connection = $connection;
        $this->numItems = $numItems;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select(['et.id', 'ev.eid', 'ev.trigger_language AS language'])
            ->from('element_version', 'ev')
            ->join('ev', 'element_tree', 'et', 'ev.eid = et.eid')
            ->orderBy('ev.created_at', 'DESC')
            ->setMaxResults($this->numItems);

        $rows = $this->connection->fetchAll($qb->getSQL());

        $data = [];

        foreach ($rows as $row) {
            $element = $this->elementService->findElement($row['eid']);
            $elementVersion = $this->elementService->findLatestElementVersion($element);
            $node = $this->treeManager->getByNodeId($row['id'])->get($row['id']);
            //$siterootId = $node->getTree()->getSiterootId();

            $baseTitle = $elementVersion->getBackendTitle($row['language']);
            $baseTitleArr = str_split($baseTitle, 16);
            $title = '';

            $first = true;
            foreach ($baseTitleArr as $chunk) {
                $title .= ($first ? '<wbr />' : '') . $chunk;
                $first = false;
            }

            $title .= ' [' . $row['id'] . ']';
            /*
                $i = 0;
                do
                {
                    $title .= ($i ? '<wbr />' : '') . substr($baseTitle, $i, $i + 16);
                    $i += 16;
                }
                while($i <= strlen($baseTitle));
            */

            /*
            $menuItem = new MWF_Core_Menu_Item_Panel();
            $menuItem->setIdentifier('Makeweb_elements_MainPanel_' . $siteroot->getTitle())
                ->setText($siteroot->getTitle())
                ->setIconClass('p-element-component-icon')
                ->setPanel('Makeweb.elements.MainPanel')
                ->setParam('siteroot_id', $siteroot->getId())
                ->setParam('title', $siteroot->getTitle())
                ->setParam('id', $node->getId())
                ->setParam('start_tid_path', '/' . implode('/', $node->getPath()))
                ->setCheck(['elements']);

            $menu = $menuItem->get();
            */
            $menu = [];

            $data[] = [
                'ident'    => $row['eid'] . '_' . $row['language'] . '_' . $row['version'],
                'eid'      => $row['eid'],
                'language' => $row['language'],
                'version'  => $row['version'],
                'title'    => strip_tags($title),
                'icon'     => $this->iconResolver->resolveTreeNode($node, $row['language']),
                'time'     => strtotime($elementVersion->getCreatedAt()->format('Y-m-d H:i:s')),
                'author'   => $elementVersion->getCreateUserId(),
                'menu'     => $menu
            ];
        }

        return $data;
    }
}