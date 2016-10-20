<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Tree;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\TreeBundle\Model\TreeInterface;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * NodeSorter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeSorter
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $sortLanguage;

    /**
     * @param Connection $connection
     * @param string     $sortLang
     */
    public function __construct(Connection $connection, $sortLang)
    {
        $this->connection = $connection;
        $this->sortLanguage = $sortLang;
    }

    /**
     * @param TreeNodeInterface $node
     *
     * @return array
     */
    public function sort(TreeNodeInterface $node)
    {
        if (!$node->getTree()->hasChildren($node)) {
            return array();
        }

        $parentId = $node->getId();
        $sortMode = $node->getSortMode();
        $sortDir = $node->getSortDir();

        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('t.id')
            ->from('tree', 't')
            ->where($queryBuilder->expr()->eq('t.parent_id', $parentId));

        switch ($sortMode) {
            case TreeInterface::SORT_MODE_TITLE:
                $queryBuilder
                    ->addSelect('evmf.backend AS sorter')
                    ->leftJoin('t', 'element', 'e', 't.type_id = e.eid')
                    ->leftJoin('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
                    ->leftJoin('ev', 'element_version_mapped_field', 'evmf', 'ev.id = evmf.element_version_id AND evmf.language = ' . $this->connection->quote($this->sortLanguage))
                    ->orderBy('evmf.backend', $sortDir);
                break;

            case TreeInterface::SORT_MODE_CREATEDATE:
                $queryBuilder
                    ->addSelect('t.modify_time AS sorter')
                    ->orderBy('t.modify_time', $sortDir);
                break;

            case TreeInterface::SORT_MODE_PUBLISHDATE:
                $queryBuilder
                    ->addSelect('to.publish_time AS sorter')
                    ->leftJoin('t', 'tree_online', 'to', 'eto.tree_id = et.id AND eto.language = ' . $this->connection->quote($this->sortLanguage))
                    ->orderBy('to.publish_time', $sortDir);
                break;

            case TreeInterface::SORT_MODE_CUSTOMDATE:
                $queryBuilder
                    ->addSelect('evmf.date AS sorter')
                    ->leftJoin('t', 'element', 'e', 't.type_id = e.eid')
                    ->leftJoin('e', 'element_version', 'ev', 'e.eid = ev.eid AND e.latest_version = ev.version')
                    ->leftJoin('ev', 'element_version_mapped_field', 'evmf', 'ev.id = evmf.element_version_id AND evmf.language = ' . $this->connection->quote($this->sortLanguage))
                    ->orderBy('evmf.date', $sortDir);
                break;

            default:
                $queryBuilder
                    ->addSelect('t.sort AS sorter')
                    ->orderBy('t.sort', 'ASC');
                break;
        }

        $result = $this->connection->fetchAll($queryBuilder->getSQL());
        $result = array_column($result, 'id');

        return $result;
    }
}
