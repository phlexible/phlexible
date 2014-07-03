<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Viability;

use Phlexible\Bundle\ElementtypeBundle\Elementtype\Elementtype;
use Phlexible\Component\Database\ConnectionManager;

/**
 * Viability manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ViabilityManager
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    private $parents = array();

    private $children = array();

    /**
     * @param ConnectionManager $connectionManager
     */
    public function __construct(ConnectionManager $connectionManager)
    {
        $this->db = $connectionManager->default;
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function getAllowedParentIds(Elementtype $elementtype)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'elementtype_apply', 'apply_under_id')
            ->where('elementtype_id = ?', $elementtype->getId());

        return $this->db->fetchCol($select);
    }

    /**
     * @param Elementtype $elementtype
     *
     * @return array
     */
    public function getAllowedChildrenIds(Elementtype $elementtype)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'elementtype_apply', 'elementtype_id')
            ->where('apply_under_id = ?', $elementtype->getId());

        return $this->db->fetchCol($select);
    }

    /**
     * Save viability
     *
     * @param Elementtype $elementtype
     * @param array       $parentIds
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function saveAllowedParentIds(Elementtype $elementtype, array $parentIds)
    {
        $this->db->delete(
            $this->db->prefix . 'elementtype_apply',
            array('elementtype_id = ?' => $elementtype->getId())
        );

        foreach ($parentIds as $parentId) {
            $this->db->insert(
                $this->db->prefix . 'elementtype_apply',
                array(
                    'elementtype_id' => $elementtype->getId(),
                    'apply_under_id' => $parentId,
                )
            );
        }
    }
}
