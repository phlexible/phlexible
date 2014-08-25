<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller\Data;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Model\ElementStructure;
use Phlexible\Bundle\ElementBundle\Model\ElementStructureValue;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data saver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DataSaver
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @param ElementService $elementService
     */
    public function __construct(ElementService $elementService)
    {
        $this->elementService = $elementService;
    }

    /**
     * Save element data
     *
     * @param Request $request
     *
     * @return array
     */
    public function save(Request $request)
    {
        $eid = $request->get('eid');
        $values = $request->request->all();

        $element = $this->elementService->findElement($eid);
        $elementtype = $this->elementService->findElementtype($element);
        $elementtypeVersion = $this->elementService->getElementtypeService()->findLatestElementtypeVersion($elementtype);
        $elementtypeStructure = $this->elementService->getElementtypeService()->findElementtypeStructure($elementtypeVersion);

        $this->structures[null] = $rootElementStructure = new ElementStructure();

        foreach ($values as $key => $value) {
            $parts = explode('__', $key);
            $fixed = $parts[0];
            $repeatableGroup = null;
            if (isset($parts[1])) {
                $repeatableGroup = $parts[1];
            }

            if (preg_match('/^field-([-a-f0-9]{36})-id-([0-9]+)$/', $fixed, $match)) {
                // existing root value
                $dsId = $match[1];
                $id = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $elementStructureValue = new ElementStructureValue($dsId, $node->getName(), $node->getType(), $value);
                $elementStructure = $this->findGroup($repeatableGroup);
                $elementStructure->setValue($elementStructureValue);
            } elseif (preg_match('/^field-([-a-f0-9]{36})-new-([0-9]+)$/', $fixed, $match)) {
                // new root value
                $dsId = $match[1];
                $id = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $elementStructureValue = new ElementStructureValue($dsId, $node->getName(), $node->getType(), $value);
                $elementStructure = $this->findGroup($repeatableGroup);
                $elementStructure->setValue($elementStructureValue);
            } elseif (preg_match('/^group-([-a-f0-9]{36})-id-([0-9]+)$/', $fixed, $match)) {
                // existing repeatable group
                $parent = $this->findGroup($repeatableGroup);
                $dsId = $match[1];
                $id = $match[2];
                $node = $elementtypeStructure->getNode($dsId);
                $this->structures[$id] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setDsId($dsId)
                    ->setId($id)
                    ->setParentDsId($parent->getDsId())
                    ->setParentName($parent->getName())
                    ->setName($node->getName());
                $parent->addStructure($elementStructure);
            } elseif (preg_match('/^group-([-a-f0-9]{36})-new-([0-9]+)$/', $fixed, $match)) {
                // new repeatable group
                $parent = $this->findGroup($repeatableGroup);
                $dsId = $match[1];
                $id = $match[2];
                $this->structures[$id] = $elementStructure = new ElementStructure();
                $elementStructure
                    ->setDsId($dsId)
                    ->setId($id)
                    ->setParentDsId($parent->getDsId())
                    ->setParentName($parent->getName())
                    ->setName('xxx');
                $parent->addStructure($elementStructure);
            }
        }

        print_r($rootElementStructure);die;
    }

    private $structures = array();

    /**
     * @param string $identifier
     *
     * @return ElementStructure
     */
    private function findGroup($identifier)
    {
        if (preg_match('/^group-([-a-f0-9]{36})-id-([0-9]+)$/', $identifier, $match)) {
            // existing repeatable group
            $dsId = $match[1];
            $id = $match[2];
            return $this->structures[$id];
        } elseif (preg_match('/^group-([-a-f0-9]{36})-new-([0-9]+)$/', $identifier, $match)) {
            // new repeatable group
            $dsId = $match[1];
            $id = $match[2];
            return $this->structures[$id];
        } else {
            return $this->structures[null];
        }
    }
}
