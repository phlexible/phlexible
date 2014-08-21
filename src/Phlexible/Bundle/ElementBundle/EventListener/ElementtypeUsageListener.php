<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeUsageEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param Connection               $connection
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(Connection $connection, SecurityContextInterface $securityContext)
    {
        $this->connection = $connection;
        $this->securityContext = $securityContext;
    }

    /**
     * @param ElementtypeUsageEvent $event
     */
    public function onElementtypeUsage(ElementtypeUsageEvent $event)
    {
        $elementtypeId = $event->getElementtype()->getId();
        $language = $this->securityContext->getToken()->getUser()->getInterfaceLanguage();

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ev.eid', 'MAX(ev.elementtype_version) AS latest_version', 'et.type')
            ->from('element_version', 'ev')
            ->join('ev', 'element', 'e', 'ev.eid = e.eid AND e.elementtype_id = ' . $qb->expr()->literal($elementtypeId))
            ->join('e', 'elementtype', 'et', 'e.elementtype_id = et.id')
            //->from(array('ev' => $this->db->prefix.'element_version'), array('eid'))
            //->join(array('et' => $this->db->prefix.'element_tree'), 'ev.eid = et.eid', array())
            ->groupBy('ev.eid');

        $rows = $this->connection->fetchAll($qb->getSQL());

        foreach ($rows as $row) {
            //$elementVersion = $manager->getLatest($eid);

            $event->addUsage(
                $row['type'] . ' element',
                $row['eid'],
                $row['eid'], //$elementVersion->getBackendTitle($defaultLanguage),
                $row['latest_version']
            );
        }
    }
}