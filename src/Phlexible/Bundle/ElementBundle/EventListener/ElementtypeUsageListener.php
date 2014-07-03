<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeUsageEvent;
use Phlexible\Component\Database\ConnectionManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

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
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param ConnectionManager        $connectionManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(ConnectionManager $connectionManager, SecurityContextInterface $securityContext)
    {
        $this->db = $connectionManager->default;
        $this->securityContext = $securityContext;
    }

    /**
     * @param ElementtypeUsageEvent $event
     */
    public function onElementtypeUsage(ElementtypeUsageEvent $event)
    {
        $elementtypeId = $event->getElementtype()->getId();
        $language = $this->securityContext->getUser()->getInterfaceLanguage();

        $select = $this->db
            ->select()
            ->distinct()
            ->from(
                array('ev' => $this->db->prefix . 'element_version'),
                array(
                    'eid',
                    $this->db->fn->expr('MAX(ev.elementtype_version) AS latest_version')
                )
            )
            ->join(
                array('e' => $this->db->prefix . 'element'),
                'ev.eid = e.eid AND e.elementtype_id = ' . $this->db->quote($elementtypeId),
                array()
            )
            ->join(
                array('et' => $this->db->prefix . 'elementtype'),
                'e.elementtype_id = et.id',
                array('type')
            )
            //->from(array('ev' => $this->db->prefix.'element_version'), array('eid'))
            //->join(array('et' => $this->db->prefix.'element_tree'), 'ev.eid = et.eid', array())
            ->group('ev.eid');

        $rows = $this->db->fetchAll($select);

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