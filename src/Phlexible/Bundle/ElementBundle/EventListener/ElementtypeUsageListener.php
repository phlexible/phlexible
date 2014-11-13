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
            ->select('ev.eid', 'es.elementtype_revision AS latest_version', 'evmf.backend AS title', 'ev.id')
            ->from('element', 'e')
            ->join('e', 'element_source', 'es', 'es.elementtype_id = e.elementtype_id')
            ->join('es', 'element_version', 'ev', 'ev.element_source_id = es.id')
            ->leftJoin('ev', 'element_version_mapped_field', 'evmf', 'ev.id = evmf.element_version_id AND evmf.language = ' . $qb->expr()->literal($language))
            ->where($qb->expr()->eq('e.elementtype_id', $qb->expr()->literal($elementtypeId)))
            ->groupBy('ev.eid');

        $rows = $this->connection->fetchAll($qb->getSQL());

        foreach ($rows as $row) {
            $event->addUsage(
                new Usage(
                    $event->getElementtype()->getType() . ' element',
                    'element',
                    $row['eid'],
                    $row['title'],
                    $row['latest_version']
                )
            );
        }
    }
}
