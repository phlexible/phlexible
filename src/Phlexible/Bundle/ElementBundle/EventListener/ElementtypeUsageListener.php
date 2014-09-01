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
use Phlexible\Bundle\ElementtypeBundle\Usage\Usage;
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
        $language = $this->securityContext->getToken()->getUser()->getInterfaceLanguage('de');

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ev.eid', 'MAX(ev.elementtype_version) AS latest_version', 'et.type', 'evmf.backend AS title1', 'et.title AS title2', 'ev.id')
            ->from('element', 'e')
            ->join('e', 'element_version', 'ev', 'ev.eid = e.eid AND ev.version = e.latest_version')
            ->leftJoin('ev', 'element_version_mapped_field', 'evmf', 'ev.id = evmf.element_version_id AND evmf.language = ' . $qb->expr()->literal($language))
            ->join('e', 'elementtype', 'et', 'e.elementtype_id = et.id')
            //->from(array('ev' => $this->db->prefix.'element_version'), array('eid'))
            //->join(array('et' => $this->db->prefix.'element_tree'), 'ev.eid = et.eid', array())
            ->where($qb->expr()->eq('e.elementtype_id', $elementtypeId))
            ->groupBy('ev.eid');

        $rows = $this->connection->fetchAll($qb->getSQL());

        foreach ($rows as $row) {
            $event->addUsage(
                new Usage(
                    $row['type'] . ' element',
                    'element',
                    $row['eid'],
                    $row['title1'] ?: '[' . $row['title2'] . ']',
                    $row['latest_version']
                )
            );
        }
    }
}