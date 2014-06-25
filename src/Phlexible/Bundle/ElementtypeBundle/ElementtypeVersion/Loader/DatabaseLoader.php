<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\Loader;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;

/**
 * Elementtype version database loader
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
    public function load(Elementtype $elementtype, $version)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'elementtype_version')
            ->where('elementtype_id = ?', $elementtype->getId())
            ->where('version = ?', $version);

        $row = $this->db->fetchRow($select);

        if (!$row) {
            return null;
        }

        return $this->mapRow($row, $elementtype)
            ->setElementtype($elementtype);
    }

    /**
     * {inheritdoc}
     */
    public function loadVersions(Elementtype $elementtype)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'elementtype_version', 'version')
            ->where('elementtype_id = ?', $elementtype->getId());

        $versions = $this->db->fetchCol($select);

        return $versions;
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     */
    public function insert(ElementtypeVersion $elementtypeVersion)
    {
        $this->db->insert(
            $this->db->prefix . 'elementtype_version',
            array(
                'elementtype_id'      => $elementtypeVersion->getElementtype()->getId(),
                'version'             => $elementtypeVersion->getVersion(),
                'default_content_tab' => $elementtypeVersion->getDefaultContentTab(),
                'metaset_id'          => $elementtypeVersion->getMetaSetId(),
                'comment'             => $elementtypeVersion->getComment(),
                'mappings'            => $elementtypeVersion->getMappings() ? json_encode($elementtypeVersion->getMappings()) : null,
                'create_user_id'      => $elementtypeVersion->getCreateUserId(),
                'created_at'          => $elementtypeVersion->getCreatedAt()->format('Y-m-d H:i:s')
            )
        );
    }

    /**
     * @param array $row
     * @return ElementtypeVersion
     */
    private function mapRow(array $row)
    {
        $elementtypeVersion = new ElementtypeVersion();
        $elementtypeVersion
            ->setVersion($row['version'])
            ->setDefaultContentTab($row['default_content_tab'])
            ->setMappings($row['mappings'] ? json_decode($row['mappings'], true) : null)
            ->setComment($row['comment'])
            ->setMetaSetId($row['metaset_id'])
            ->setCreatedAt(new \DateTime($row['created_at']))
            ->setCreateUserId($row['create_user_id']);

        return $elementtypeVersion;
    }
}