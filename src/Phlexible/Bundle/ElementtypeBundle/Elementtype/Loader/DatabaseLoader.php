<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Elementtype\Loader;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Elementtype\ElementtypeCollection;

/**
 * Elementtype database loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DatabaseLoader implements LoaderInterface
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @param ConnectionManager $dbPool
     */
    public function __construct(ConnectionManager $dbPool)
    {
        $this->db = $dbPool->default;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'elementtype');

        $rows = $this->db->fetchAll($select);

        $elementtypes = new ElementtypeCollection($this->mapRows($rows));

        return $elementtypes;
    }

    /**
     * {@inheritdoc}
     */
    public function insert(Elementtype $elementtype)
    {
        $this->db->insert(
            $this->db->prefix . 'elementtype',
            array(
                'unique_id'      => $elementtype->getUniqueId(),
                'type'           => $elementtype->getType(),
                'title'          => $elementtype->getTitle(),
                'icon'           => $elementtype->getIcon(),
                'default_tab'    => $elementtype->getDefaultTab(),
                'hide_children'  => $elementtype->getHideChildren() ? 1 : 0,
                'latest_version' => $elementtype->getLatestVersion(),
                'create_user_id' => $elementtype->getCreateUserId(),
                'created_at'     => $elementtype->getCreatedAt()->format('Y-m-d H:i:s'),
            )
        );

        $elementtype->setId($this->db->lastInsertId($this->db->prefix . 'elementtype'));
    }

    /**
     * {@inheritdoc}
     */
    public function update(Elementtype $elementtype)
    {
        $this->db->update(
            $this->db->prefix . 'elementtype',
            array(
                'unique_id'      => $elementtype->getUniqueId(),
                'title'          => $elementtype->getTitle(),
                'icon'           => $elementtype->getIcon(),
                'default_tab'    => $elementtype->getDefaultTab(),
                'hide_children'  => $elementtype->getHideChildren() ? 1 : 0,
                'latest_version' => $elementtype->getLatestVersion(),
            ),
            array(
                'id = ?' => $elementtype->getId(),
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Elementtype $elementtype)
    {
        $this->db->delete(
            $this->db->prefix . 'elementtype',
            array(
                'id = ?' => $elementtype->getId()
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function softDelete(Elementtype $elementtype)
    {
        $this->db->update(
            $this->db->prefix.'elementtype',
            array(
                'deleted' => true
            ),
            array(
                'id = ?' => $elementtype->getId()
            )
        );
    }

    /**
     * @param array $rows
     *
     * @return Elementtype[]
     */
    private function mapRows(array $rows)
    {
        $elementtypes = array();
        foreach ($rows as $row) {
            $elementtypes[] = $this->mapRow($row);
        }

        return $elementtypes;
    }

    /**
     * @param array $row
     *
     * @return Elementtype
     */
    private function mapRow(array $row)
    {
        $elementtype = new Elementtype();
        $elementtype
            ->setId($row['id'])
            ->setUniqueId($row['unique_id'])
            ->setType($row['type'])
            ->setTitle($row['title'])
            ->setIcon($row['icon'])
            ->setDefaultTab($row['default_tab'])
            ->setHideChildren($row['hide_children'] ? true : false)
            ->setLatestVersion($row['latest_version'])
            ->setCreatedAt(new \DateTime($row['created_at']))
            ->setCreateUserId($row['create_user_id'])
        ;

        return $elementtype;
    }
}