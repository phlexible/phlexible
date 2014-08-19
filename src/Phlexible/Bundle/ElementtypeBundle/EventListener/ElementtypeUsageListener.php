<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\EventListener;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeUsageEvent;

/**
 * Elementtype usage listeners
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeUsageListener
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param ElementtypeUsageEvent $event
     */
    public function onElementtypeUsage(ElementtypeUsageEvent $event)
    {
        $elementtypeId = $event->getElementtype()->getId();

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select(array('es.elementtype_id', 'MAX(es.reference_version) AS latest_version', 'e.title', 'e.type'))
            ->from('elementtype_structure', 'es')
            ->join('es', 'elementtype', 'e', 'es.elementtype_id = e.id')
            ->where($qb->expr()->eq('es.reference_id', $elementtypeId))
            ->groupBy('es.elementtype_id');

        $rows = $this->connection->fetchAll($qb->getSQL());

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