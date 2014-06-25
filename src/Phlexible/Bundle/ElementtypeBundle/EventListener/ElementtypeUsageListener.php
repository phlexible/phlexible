<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\EventListener;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeUsageEvent;

/**
 * Elementtype usage listeners
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeUsageListener
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @param ConnectionManager $connectionManager
     */
    public function __construct(ConnectionManager $connectionManager)
    {
        $this->db = $connectionManager->default;
    }

    /**
     * @param ElementtypeUsageEvent $event
     */
    public function onElementtypeUsage(ElementtypeUsageEvent $event)
    {
        $elementtypeId = $event->getElementtype()->getId();

        $select = $this->db
            ->select()
            ->distinct()
            ->from(
                array('es' => $this->db->prefix.'elementtype_structure'),
                array(
                    'elementtype_id',
                    $this->db->fn->expr('MAX(es.reference_version) AS latest_version')
                )
            )
            ->join(
                array('e' => $this->db->prefix.'elementtype'),
                'es.elementtype_id = e.id',
                array('title', 'type')
            )
            ->where('es.reference_id = ?', $elementtypeId)
            ->group('es.elementtype_id');

        $rows = $this->db->fetchAll($select);

        foreach ($rows as $row) {
            $event->addUsage(
                  $row['type'] . ' elementtype',
                  $row['elementtype_id'],
                  $row['title'],
                  $row['latest_version']
            );
        }
    }
}