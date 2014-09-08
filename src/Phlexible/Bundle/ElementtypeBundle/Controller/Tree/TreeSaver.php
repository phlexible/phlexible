<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller\Tree;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeVersion;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Tree saver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TreeSaver
{
    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @param ElementtypeService $elementtypeService
     */
    public function __construct(ElementtypeService $elementtypeService)
    {
        $this->elementtypeService = $elementtypeService;
    }

    /**
     * Save an Element Type data tree
     *
     * @param Request       $request
     * @param UserInterface $user
     *
     * @return ElementtypeVersion
     */
    public function save(Request $request, UserInterface $user)
    {
        $elementtypeId = $request->get('element_type_id', false);
        $data = json_decode($request->get('data'), true);

        if (!$elementtypeId) {
            throw new \Exception('No elementtype ID.');
        }

        $rootData = $data[0];
        $rootType = $rootData['type'];
        $rootProperties = $rootData['properties'];
        $rootConfig = $rootProperties['root'];
        $rootMappings = !empty($rootProperties['mappings']) ? $rootProperties['mappings'] : null;
        $rootDsId = !empty($rootData['ds_id']) ? $rootData['ds_id'] : Uuid::generate();

        if (!isset($rootData['type']) || ($rootData['type'] != 'root' && $rootData['type'] != 'referenceroot')) {
            throw new \Exception('Invalid root node.');
        }

        if (!isset($rootConfig['unique_id']) || !trim($rootConfig['unique_id'])) {
            throw new \Exception('No unique ID.');
        }

        $uniqueId = trim($rootConfig['unique_id']);
        $title = trim($rootConfig['title']);
        $icon = trim($rootConfig['icon']);
        $hideChildren = !empty($rootConfig['hide_children']);
        $defaultTab = strlen($rootConfig['default_tab']) ? $rootConfig['default_tab'] : null;
        $defaultContentTab = strlen($rootConfig['default_content_tab']) ? $rootConfig['default_content_tab'] : null;
        $comment = trim($rootConfig['comment']) ?: null;

        $elementtype = $this->elementtypeService->findElementtype($elementtypeId);

        $priorElementtypeVersion = $this->elementtypeService->findLatestElementtypeVersion($elementtype);

        $elementtypeVersion = clone $priorElementtypeVersion;
        $elementtypeVersion
            ->setVersion($elementtypeVersion->getVersion() + 1)
            ->setDefaultContentTab($defaultContentTab)
            ->setMappings($rootMappings)
            ->setComment($comment)
            ->setCreateUserId($user->getId())
            ->setCreatedAt(new \DateTime());

        $elementtype
            ->setUniqueId($uniqueId)
            ->setTitle($title)
            ->setIcon($icon)
            ->setHideChildren($hideChildren)
            ->setDefaultTab($defaultTab)
            ->setLatestVersion($elementtypeVersion->getVersion());

        $elementtypeStructure = null;
        if (isset($rootData['children'])) {
            $fieldData = $rootData['children'];
            $elementtypeStructure = $this->buildElementtypeStructure($elementtypeVersion, $rootType, $rootDsId, $user, $fieldData);
        }

        $this->elementtypeService->updateElementtype($elementtype, false);

        if ($elementtypeStructure) {
            $this->elementtypeService->updateElementtypeStructure($elementtypeStructure, false);
        }

        $this->elementtypeService->updateElementtypeVersion($elementtypeVersion, true);

        // update elementtypes that use this elementtype as reference

        if ($elementtype->getType() === 'reference') {
            $this->updateElementtypesUsingReference($elementtype, $user->getId());
        }

        return $elementtypeVersion;
    }

    /**
     * @param Elementtype $referenceElementtype
     * @param string      $userId
     */
    private function updateElementtypesUsingReference(Elementtype $referenceElementtype, $userId)
    {
        $elementtypes = $this->elementtypeService->findElementtypesUsingReferenceElementtype($referenceElementtype);
        foreach ($elementtypes as $elementtype) {
            $elementtypeVersion = clone $this->elementtypeService->findLatestElementtypeVersion($elementtype);
            $latestElementtypeStructure = $this->elementtypeService->findElementtypeStructure($elementtypeVersion);

            $elementtypeVersion
                ->setId(null)
                ->setVersion($elementtypeVersion->getVersion() + 1)
                ->setElementtype($elementtype)
                ->setCreatedAt(new \Datetime())
                ->setCreateUserId($userId);

            $elementtype
                ->setLatestVersion($elementtypeVersion->getVersion());

            $elementtypeStructure = new ElementtypeStructure();

            $rii = new \RecursiveIteratorIterator($latestElementtypeStructure->getIterator(), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $latestNode) {
                if ($latestNode->isReferenced()) {
                    continue;
                }

                /* @var $node ElementtypeStructureNode */
                $node = clone $latestNode;
                $node
                    ->setId(null)
                    ->setElementtype($elementtype)
                    ->setVersion($elementtypeVersion->getVersion())
                    ->setElementtypeStructure($elementtypeStructure)
                    ->setParentNode($elementtypeStructure->getNode($node->getParentDsId()));

                if ($node->isReference() && $node->getReferenceElementtype()->getId() === $referenceElementtype->getId()) {
                    $node->setReferenceVersion($referenceElementtype->getLatestVersion());
                }

                $elementtypeStructure->addNode($node);
            }

            $this->elementtypeService->updateElementtype($elementtype, true);
            $this->elementtypeService->updateElementtypeVersion($elementtypeVersion, true);
            $this->elementtypeService->updateElementtypeStructure($elementtypeStructure, true);
        }
    }

    /**
     * @param ElementtypeVersion $elementtypeVersion
     * @param string             $rootType
     * @param string             $rootDsId
     * @param UserInterface      $user
     * @param array              $data
     *
     * @return ElementtypeStructure
     */
    private function buildElementtypeStructure(ElementtypeVersion $elementtypeVersion, $rootType, $rootDsId, UserInterface $user, array $data)
    {
        $elementtype = $elementtypeVersion->getElementtype();

        $elementtypeStructure = new ElementtypeStructure();
        $elementtypeStructure
            ->setElementtypeVersion($elementtypeVersion);

        $sort = 1;

        $rootNode = new ElementtypeStructureNode();
        $rootNode
            ->setElementtype($elementtype)
            ->setVersion($elementtypeVersion->getVersion())
            ->setElementtypeStructure($elementtypeStructure)
            ->setDsId($rootDsId)
            ->setType($rootType)
            ->setName('root')
            ->setSort($sort);

        $elementtypeStructure->addNode($rootNode);

        $this->iterateData($elementtypeVersion, $elementtypeStructure, $rootNode, $user, $sort, $data);

        return $elementtypeStructure;
    }

    /**
     * @param ElementtypeVersion       $elementtypeVersion
     * @param ElementtypeStructure     $elementtypeStructure
     * @param ElementtypeStructureNode $rootNode
     * @param UserInterface            $user
     * @param int                      $sort
     * @param array                    $data
     *
     * @return mixed
     */
    private function iterateData(
        ElementtypeVersion $elementtypeVersion,
        ElementtypeStructure $elementtypeStructure,
        ElementtypeStructureNode $rootNode,
        UserInterface $user,
        $sort,
        array $data)
    {
        foreach ($data as $row) {
            if (!$row['parent_ds_id']) {
                $row['parent_ds_id'] = $rootNode->getDsId();
            }
            $node = new ElementtypeStructureNode();
            $parentNode = $elementtypeStructure->getNode($row['parent_ds_id']);

            $node
                ->setElementtype($elementtypeVersion->getElementtype())
                ->setVersion($elementtypeVersion->getVersion())
                ->setElementtypeStructure($elementtypeStructure)
                ->setDsId(!empty($row['ds_id']) ? $row['ds_id'] : Uuid::generate())
                ->setParentDsId($parentNode->getDsId())
                ->setParentNode($parentNode)
                ->setSort(++$sort);

            if ($row['type'] == 'reference' && isset($row['reference']['new'])) {
                $firstChild = $row['children'][0];
                $referenceElementtypeVersion = $this->elementtypeService->createElementtype(
                    'reference',
                    'reference_' . $firstChild['properties']['field']['working_title'] . '_' . uniqid(),
                    'Reference ' . $firstChild['properties']['field']['working_title'],
                    '_fallback.gif',
                    $user->getId(),
                    false
                );
                $referenceElementtype = $referenceElementtypeVersion->getElementtype();
                $referenceRootDsId = Uuid::generate();
                foreach ($row['children'] as $index => $referenceRow) {
                    $row['children'][$index]['parent_ds_id'] = $referenceRootDsId;
                }
                $referenceElementtypeStructure = $this->buildElementtypeStructure(
                    $referenceElementtypeVersion,
                    'referenceroot',
                    $referenceRootDsId,
                    $user,
                    $row['children']
                );

                $this->elementtypeService->updateElementtypeStructure($referenceElementtypeStructure, false);

                $node
                    ->setType('reference')
                    ->setName('reference_' . $referenceElementtype->getId())
                    ->setReferenceElementtype($referenceElementtype)
                    ->setReferenceVersion($referenceElementtypeVersion->getVersion());

                $elementtypeStructure->addNode($node);
            } elseif ($row['type'] == 'reference') {
                $referenceElementtype = $this->elementtypeService->findElementtype($row['reference']['refID']);

                $node
                    ->setType('reference')
                    ->setName('reference_' . $referenceElementtype->getId())
                    ->setReferenceElementtype($referenceElementtype)
                    ->setReferenceVersion($row['reference']['refVersion']);

                $elementtypeStructure->addNode($node);
            } else {
                $properties = $row['properties'];

                $node
                    ->setType($properties['field']['type'])
                    ->setName(trim($properties['field']['working_title']))
                    ->setComment(trim($properties['field']['comment']) ?: null)
                    ->setConfiguration(!empty($properties['configuration']) ? $properties['configuration'] : null)
                    ->setValidation(!empty($properties['validation']) ? $properties['validation'] : null)
                    ->setLabels(!empty($properties['labels']) ? $properties['labels'] : null)
                    ->setOptions(!empty($properties['options']) ? $properties['options'] : null)
                    ->setContentChannels(
                        !empty($properties['content_channels']) ? $properties['content_channels'] : null
                    );

                $elementtypeStructure->addNode($node);

                if (!empty($row['children'])) {
                    $sort = $this->iterateData($elementtypeVersion, $elementtypeStructure, $rootNode, $user, $sort, $row['children']);
                }
            }
        }

        return $sort;
    }
}
