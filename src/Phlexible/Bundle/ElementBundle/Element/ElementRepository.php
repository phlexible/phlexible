<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Element;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\ElementBundle\ElementsMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementRepository
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
     * Find element by ID
     *
     * @param int $eid
     *
     * @throws \Exception
     * @return Element
     */
    public function find($eid)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'element')
            ->where('eid = ?', $eid)
        ;

        $row = $this->db->fetchRow($select);

        if (!$row) {
            throw new \Exception("Element EID $eid not found.");
        }

        return $this->mapRow($row);
    }

    /**
     * Find elements
     *
     * @param array  $criteria
     * @param string $order
     * @param int    $limit
     * @param int    $offset
     *
     * @return Element[]
     */
    public function findBy(array $criteria = array(), $order = null, $limit = null, $offset = null)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'element')
        ;

        foreach ($criteria as $key => $value) {
            if (is_array($value)) {
                $select->where($this->db->quoteIdentifier($key) . ' IN (?)', $value);
            } else {
                $select->where($this->db->quoteIdentifier($key) . ' = ?', $value);
            }
        }

        if ($order) {
            $select->order($order);
        }

        if ($limit) {
            if ($offset) {
                $select->limit($limit, $offset);
            } else {
                $select->limit($limit);
            }
        }

        $rows = $this->db->fetchAll($select);

        return $this->mapRows($rows);
    }

    /**
     * @param array $rows
     *
     * @return Element[]
     */
    private function mapRows(array $rows)
    {
        $elements = array();

        foreach ($rows as $row) {
            $elements[] = $this->mapRow($row);
        }

        return $elements;
    }

    private $map = array();

    /**
     * @param array $row
     *
     * @return Element
     */
    private function mapRow(array $row)
    {
        if (isset($this->map[$row['eid']])) {
            return $this->map[$row['eid']];
        }

        $element = new Element();
        $element
            ->setEid($row['eid'])
            ->setUniqueId($row['unique_id'])
            ->setElementtypeId($row['elementtype_id'])
            ->setMasterLanguage($row['masterlanguage'])
            ->setLatestVersion($row['latest_version'])
            ->setCreatedAt(new \DateTime($row['create_time']))
            ->setCreateUserId($row['create_user_id'])
        ;

        $this->map[$row['eid']] = $element;

        return $element;
    }

    /**
     * Find element by unique ID
     *
     * @param string $uniqueId
     * @throws \Exception
     *
     * @return Element
     */
    public function findByUniqueID($uniqueId)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'element')
            ->where('unique_id = ?', $uniqueId)
        ;

        $row = $this->db->fetchRow($select);

        if (!$row) {
            throw new \Exception("Element unique ID $uniqueId not found.");
        }

        return $this->mapRow($row);
    }

    /**
     * Save element
     *
     * @param Element $element
     *
     * @return $this
     */
    public function save(Element $element)
    {
        if (!$element->getEid()) {
            $event = new BeforeCreateEvent($element);
            if (!$this->dispatcher->dispatch($event))
            {
                throw new \Exception('Create canceled by callback.');
            }

            $this->loader->insert($elementtype);

            // post event
            $event = new CreateEvent($elementtype);
            $this->dispatcher->dispatch($event);

            // post message
            $message = new ElementtypesMessage('Element Type "'.$elementtype->getId().' created.');
            $message->post();
        } else {
            $this->loader->update($element);

            // post message
            $message = new ElementtypesMessage('Element Type "'.$elementtype->getId().' updated.');
            $message->post();
        }

        return $this;
    }

    /**
     * Delete element
     *
     * @param Element $element
     * @return $this
     * @throws \Exception
     */
    public function delete(Element $element)
    {
        // post before event
        $event = new BeforeDeleteEvent($elementtype);
        if (!$this->dispatcher->dispatch($event))
        {
            throw new \Exception('Delete canceled by listener.');
        }

        $delete = true;

        if ($elementtype->getType() == ElementtypeVersion::TYPE_REFERENCE)
        {
            $db = MWF_Registry::getContainer()->dbPool->default;
            $select = $db->select()
                         ->distinct()
                         ->from($db->prefix.'elementtype_structure', array('element_type_id', new Zend_Db_Expr('MAX(version) AS max_version')))
                         ->where('reference_id = ?', $elementtype->getId())
                         ->group('element_type_id');

            $result = $db->fetchAll($select);

            if (count($result))
            {
                $delete = false;

                $select = $db->select()
                             ->from($db->prefix . 'elementtype', 'latest_version')
                             ->where('element_type_id = ?');

                foreach($result as $row)
                {
                    $latestElementTypeVersion = $db->fetchOne($select, $row['element_type_id']);

                    if ($latestElementTypeVersion == $row['max_version'])
                    {
                        throw new \Exception('Reference in use, can\'t delete.');
                    }
                }
            }
        }

        if ($delete)
        {
            $this->loader->delete($elementtype);

            // send message
            $message = new ElementsMessage('Element type "'.$elementtype->getId().'" deleted.');
            $message->post();
        }
        else
        {
            $this->loader->softDelete($elementtype);

            // send message
            $message = new ElementsMessage('Element type "'.$elementtype->getId().'" soft deleted.');
            $message->post();
        }

        // post event
        $event = new DeleteEvent($elementtype);
        $this->dispatcher->dispatch($event);

        return $this;
    }
}
