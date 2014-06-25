<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Loader;

use Phlexible\Component\Database\ConnectionManager;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeVersion\ElementtypeVersion;

/**
 * Elementtype structure database loader
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
     * @param ConnectionManager  $dbPool
     */
    public function __construct(ConnectionManager $dbPool)
    {
        $this->db = $dbPool->default;
    }

    /**
     * {@inheritdoc}
     */
    public function load(ElementtypeVersion $elementTypeVersion, $refererDsId = null)
    {
        $structure = new ElementtypeStructure();
        $structure
            ->setElementTypeVersion($elementTypeVersion);

        $this->doLoad(
            $structure,
            $elementTypeVersion->getElementtype()->getId(),
            $elementTypeVersion->getVersion(),
            $refererDsId
        );

        return $structure;
    }

    /**
     * @param ElementtypeStructure     $structure
     * @param integer                  $id
     * @param integer                  $version
     * @param ElementtypeStructureNode $referenceParentNode
     */
    private function doLoad(ElementtypeStructure $structure, $id, $version, $referenceParentNode = null)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'elementtype_structure')
            ->where('elementtype_id = ?', $id)
            ->where('elementtype_version = ?', $version)
            ->order('sort ASC');

        $rows = $this->db->fetchAll($select);

        foreach ($rows as $row) {
            $parentId = $row['parent_id'];
            $parentDsId = $row['parent_ds_id'];
            if ($referenceParentNode) {
                $parentId = $referenceParentNode->getId();
                $parentDsId = $referenceParentNode->getDsId();
                $referenceParentNode = null;
            }

            $node = new ElementtypeStructureNode();
            $node
                ->setElementtypeStructure($structure)
                ->setId($row['id'])
                ->setParentId($parentId)
                ->setDsId($row['ds_id'])
                ->setParentDsId($parentDsId)
                ->setReferenceId($row['reference_id'])
                ->setReferenceVersion($row['reference_version'])
                ->setType($row['type'])
                ->setName($row['name'])
                ->setComment($row['comment'])
                ->setConfiguration($row['configuration'] ? json_decode($row['configuration'], true) : null)
                ->setValidation($row['validation'] ? json_decode($row['validation'], true) : null)
                ->setLabels($row['labels'] ? json_decode($row['labels'], true) : null)
                ->setOptions($row['options'] ? json_decode($row['options'], true) : null)
                ->setContentChannels($row['content_channels'] ? json_decode($row['content_channels'], true) : null);

            $structure->addNode($node);

            if ($node->isReference()) {
                $this->doLoad($structure, $node->getReferenceId(), $node->getReferenceVersion(), $node);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function insert(ElementtypeStructure $elementtypeStructure)
    {
        /*
        if (!$this->getParentId() && $this->getParentDsId()) {
            $msg = 'Disambiguous parent information';
            throw new \Exception($msg);
        }
        */

        $rii = new \RecursiveIteratorIterator($elementtypeStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

        $sort = 1;
        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */

            $data = array(
                'ds_id'               => $node->getDsId(),
                'elementtype_id'      => $elementtypeStructure->getElementtypeVersion()->getElementtype()->getId(),
                'elementtype_version' => $elementtypeStructure->getElementtypeVersion()->getVersion(),
                'parent_id'           => $node->getParentId(),
                'parent_ds_id'        => $node->getParentDsId(),
                'name'                => $node->getName() ? $node->getName() : '',
                'type'                => $node->getType(),
                'sort'                => $sort,
                'reference_id'        => $node->getReferenceId(),
                'reference_version'   => $node->getReferenceVersion(),
                'comment'             => $node->getComment(),
                'configuration'       => $node->getConfiguration() ? json_encode($node->getConfiguration()) : null,
                'validation'          => $node->getValidation() ? json_encode($node->getValidation()) : null,
                'labels'              => $node->getLabels() ? json_encode($node->getLabels()) : null,
                'options'             => $node->getOptions() ? json_encode($node->getOptions()) : null,
                'content_channels'    => $node->getContentChannels() ? json_encode($node->getContentChannels()) : null,
            );

            $this->db->insert($this->db->prefix . 'elementtype_structure', $data);

            $node->setId($this->db->lastInsertId());

            $sort++;
        }
    }
}