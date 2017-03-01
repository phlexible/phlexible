<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\Serializer;

use Phlexible\Bundle\ElementtypeBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;

/**
 * Serializer interface.
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

        $nodaDatas[] = [];

        foreach ($rii as $node) {
            /* @var $node ElementtypeStructureNode */

            $nodeData = $nodeDatas[$node->getDsId()] = new \ArrayObject(
                [
                    'comment' => $node->getComment(),
                    'configuration' => $node->getConfiguration(),
                    'dsId' => $node->getDsId(),
                    'id' => md5(serialize($node)),
                    'labels' => $this->normalizeLabels($node),
                    'name' => $node->getName(),
                    'parentDsId' => $node->getParentDsId(),
                    'parentId' => md5(serialize($node->getParentNode())),
                    'referenceId' => $node->getReferenceElementtypeId() ? $node->getReferenceElementtypeId() : null,
                    'referenceVersion' => $node->getReferenceElementtypeId() ? 1 : null,
                    'type' => $node->getType(),
                    'validation' => $node->getValidation(),
                    'children' => [],
                ],
                \ArrayObject::ARRAY_AS_PROPS
            );

            if ($node->getParentDsId()) {
                $nodeDatas[$node->getParentDsId()]['children'][] = $nodeData;
            } elseif (!in_array($node->getType(), ['referenceroot', 'reference'])) {
                if (!empty($rootNode)) {
                    throw new InvalidArgumentException('duplicate root: '.print_r($nodeData, 1));
                }
                $rootNode = $nodeData;
            }
        }

        return [(array) $rootNode];
    }

    /**
     * @param ElementtypeStructureNode $node
     *
     * @return array
     */
    private function normalizeLabels(ElementtypeStructureNode $node)
    {
        $labels = $node->getLabels();

        $labels += [
            'fieldLabel' => [],
            'contextHelp' => [],
            'prefix' => [],
            'suffix' => [],
        ];

        return $labels;
    }
}
