<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Serializer;

use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeStructureNode;

/**
 * Serializer interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ArraySerializer implements SerializerInterface
{
    /**
     * {@inheritdoc}
     */
    public function serialize(ElementtypeStructure $elementtypeStructure)
    {
        if (!$elementtypeStructure->getRootNode()) {
            return null;
        }

        $rii = new \RecursiveIteratorIterator($elementtypeStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);

        $nodaDatas[] = array();

        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */

            $nodeData = $nodeDatas[$node->getId()] = new \ArrayObject(
                array(
                    'comment'          => $node->getComment(),
                    'configuration'    => $node->getConfiguration(),
                    'contentchannels'  => $node->getContentChannels(),
                    'dsId'             => $node->getDsId(),
                    'id'               => $node->getId(),
                    'labels'           => $this->normalizeLabels($node),
                    'name'             => $node->getName(),
                    'options'          => $node->getOptions(),
                    'parentDsId'       => $node->getParentDsId(),
                    'parentId'         => $node->getParentId(),
                    'referenceId'      => $node->getReferenceElementtype() ? $node->getReferenceElementtype()->getId() : null,
                    'referenceVersion' => $node->getReferenceVersion() ?: null,
                    'type'             => $node->getType(),
                    'validation'       => $node->getValidation(),
                    'children'         => array()
                ),
                \ArrayObject::ARRAY_AS_PROPS
            );

            if ($node->getParentId()) {
                $nodeDatas[$node->getParentId()]['children'][] = $nodeData;
            } elseif (!in_array($node->getType(), array('referenceroot', 'reference'))) {
                if (!empty($rootNode)) {
                    throw new \Exception('duplicate root: ' . print_r($nodeData, 1));
                }
                $rootNode = $nodeData;
            }
        }

        return array((array) $rootNode);
    }

    /**
     * @param ElementtypeStructureNode $node
     *
     * @return array
     */
    private function normalizeLabels(ElementtypeStructureNode $node)
    {
        $labels = $node->getLabels();

        $labels += array(
            'fieldlabel' => array(),
            'context_help' => array(),
            'prefix' => array(),
            'suffix' => array()
        );

        return $labels;
    }
}