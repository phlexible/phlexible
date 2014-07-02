<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersion;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\ElementBundle\Element\Element;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element version repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementVersionRepository
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param ConnectionManager        $connectionManager
     */
    public function __construct(EventDispatcherInterface $dispatcher, ConnectionManager $connectionManager)
    {
        $this->dispatcher = $dispatcher;
        $this->db = $connectionManager->default;
    }

    /**
     * @param Element $element
     * @param int     $version
     *
     * @return ElementVersion
     */
    public function find(Element $element, $version = null)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'element_version')
            ->where('eid = ?', $element->getEid())
            ->where('version = ?', $version)
        ;

        $row = $this->db->fetchRow($select);

        if (!$row) {
            throw new \Exception("Element EID {$element->getEid()} version $version not found.");
        }

        return $this->mapRow($row, $element);
    }

    /**
     * @param array   $rows
     * @param Element $element
     *
     * @return ElementVersion[]
     */
    private function mapRows(array $rows, Element $element)
    {
        $elementVersions = array();

        foreach ($rows as $row) {
            $elementVersions[] = $this->mapRow($row, $element);
        }

        return $elementVersions;
    }

    private $map = array();

    /**
     * @param array   $row
     * @param Element $element
     *
     * @return ElementVersion
     */
    private function mapRow(array $row, Element $element)
    {
        if (isset($this->map[$element->getEid() . '_' . $row['version']])) {
            return $this->map[$element->getEid() . '_' . $row['version']];
        }

        $elementVersion = new ElementVersion();
        $elementVersion
            ->setElement($element)
            ->setVersion($row['version'])
            ->setComment($row['comment'])
            ->setMappedFields($row['mapped_fields'] ? json_decode($row['mapped_fields'], true) : null)
            ->setCreatedAt(new \DateTime($row['create_time']))
            ->setCreateUserId($row['create_uid'])
        ;

        $this->map[$element->getEid() . '_' . $row['version']] = $elementVersion;

        return $elementVersion;
    }

    /**
     * @param Element $element
     *
     * @return array
     */
    public function getVersions(Element $element)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'element_version', 'version')
            ->where('eid = ?', $element->getEid())
        ;

        $versions = array();
        foreach ($this->db->fetchCol($select) as $version) {
            $versions[] = (int) $version;
        }

        return $versions;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @throws \Exception
     */
    public function save(ElementVersion $elementVersion)
    {
        $beforeEvent = new BeforeVersionCreateEventEvent($elementVersion);
        if (!$this->dispatcher->dispatch($beforeEvent)) {
            throw new \Exception('Canceled by listener.');
        }

        $this->loader->insert($elementVersion);

        $event = new VersionCreateEvent($elementVersion);
        $this->dispatcher->dispatch($beforeEvent);
    }
}
