<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller\Tree;

use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Exception\InvalidArgumentException;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
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
     * @throws InvalidArgumentException
     * @return Elementtype
     */
    public function save(Request $request, UserInterface $user)
    {
        $elementtypeId = $request->get('element_type_id', false);
        $data = json_decode($request->get('data'), true);

        if (!$elementtypeId) {
            throw new InvalidArgumentException('No elementtype ID.');
        }

        $rootData = $data[0];
        $rootType = $rootData['type'];
        $rootProperties = $rootData['properties'];
        $rootConfig = $rootProperties['root'];
        $rootMappings = !empty($rootProperties['mappings']) ? $rootProperties['mappings'] : null;
        $rootDsId = !empty($rootData['ds_id']) ? $rootData['ds_id'] : Uuid::generate();

        if (!isset($rootData['type']) || ($rootData['type'] != 'root' && $rootData['type'] != 'referenceroot')) {
            throw new InvalidArgumentException('Invalid root node.');
        }

        if (!isset($rootConfig['unique_id']) || !trim($rootConfig['unique_id'])) {
            throw new InvalidArgumentException('No unique ID.');
        }

        $uniqueId = trim($rootConfig['unique_id']);
        $title = trim($rootConfig['title']);
        $icon = trim($rootConfig['icon']);
        $hideChildren = !empty($rootConfig['hide_children']);
        $defaultTab = strlen($rootConfig['default_tab']) ? $rootConfig['default_tab'] : null;
        $defaultContentTab = strlen($rootConfig['default_content_tab']) ? $rootConfig['default_content_tab'] : null;
        $metasetId = strlen($rootConfig['metaset']) ? $rootConfig['metaset'] : null;
        $comment = trim($rootConfig['comment']) ?: null;

        $elementtype = $this->elementtypeService->findElementtype($elementtypeId);
        $elementtype
            ->setRevision($elementtype->getRevision() + 1)
            ->setUniqueId($uniqueId)
            ->setTitle('de', $title)
            ->setTitle('en', $title)
            ->setIcon($icon)
            ->setHideChildren($hideChildren)
            ->setDefaultTab($defaultTab)
            ->setDefaultContentTab($defaultContentTab)
            ->setMetaSetId($metasetId)
            ->setMappings($rootMappings)
            ->setComment($comment)
            ->setModifyUserId($user->getId())
            ->setModifiedAt(new \DateTime())
        ;

        $elementtypeStructure = null;
        if (isset($rootData['children'])) {
            $fieldData = $rootData['children'];
            $elementtypeStructure = $this->buildElementtypeStructure($rootType, $rootDsId, $user, $fieldData);
            $elementtype->setStructure($elementtypeStructure);
        }

        $this->elementtypeService->updateElementtype($elementtype, false);

        // update elementtypes that use this elementtype as reference

        /*
        if ($elementtype->getType() === 'reference') {
            $this->updateElementtypesUsingReference($elementtype, $user->getId());
        }
        */

        return $elementtype;
    }

    /**
     * @param string        $rootType
     * @param string        $rootDsId
     * @param UserInterface $user
     * @param array         $data
     *
     * @return ElementtypeStructure
     */
    private function buildElementtypeStructure($rootType, $rootDsId, UserInterface $user, array $data)
    {
        $elementtypeStructure = new ElementtypeStructure();

        $sort = 1;

        $rootNode = new ElementtypeStructureNode();
        $rootNode
            ->setDsId($rootDsId)
            ->setType($rootType)
            ->setName('root')
        //    ->setSort($sort++)
        ;

        $elementtypeStructure->addNode($rootNode);

        $this->iterateData($elementtypeStructure, $rootNode, $user, $sort, $data);

        return $elementtypeStructure;
    }

    /**
     * @param ElementtypeStructure     $elementtypeStructure
     * @param ElementtypeStructureNode $rootNode
     * @param UserInterface            $user
     * @param int                      $sort
     * @param array                    $data
     *
     * @return mixed
     */
    private function iterateData(
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
                ->setDsId(!empty($row['ds_id']) ? $row['ds_id'] : Uuid::generate())
                ->setParentDsId($parentNode->getDsId())
                ->setParentNode($parentNode)
            //    ->setSort(++$sort)
            ;

            if ($row['type'] == 'reference' && isset($row['reference']['new'])) {
                $firstChild = $row['children'][0];

                $referenceRootDsId = Uuid::generate();
                foreach ($row['children'] as $index => $referenceRow) {
                    $row['children'][$index]['parent_ds_id'] = $referenceRootDsId;
                }
                $referenceElementtypeStructure = $this->buildElementtypeStructure(
                    'referenceroot',
                    $referenceRootDsId,
                    $user,
                    $row['children']
                );

                $referenceElementtype = $this->elementtypeService->createElementtype(
                    'reference',
                    'reference_' . $firstChild['properties']['field']['working_title'] . '_' . uniqid(),
                    'Reference ' . $firstChild['properties']['field']['working_title'],
                    '_fallback.gif',
                    $referenceElementtypeStructure,
                    $user->getId(),
                    false
                );

                $node
                    ->setType('reference')
                    ->setName('reference_' . $referenceElementtype->getUniqueId())
                    ->setReferenceElementtypeId($referenceElementtype->getUniqueId())
                    //->setReferenceVersion($referenceElementtypeVersion->getVersion())
                ;

                $elementtypeStructure->addNode($node);
            } elseif ($row['type'] == 'reference') {
                $referenceElementtype = $this->elementtypeService->findElementtype($row['reference']['refID']);

                $node
                    ->setType('reference')
                    ->setName('reference_' . $referenceElementtype->getId())
                    ->setReferenceElementtypeId($referenceElementtype->getId())
                //    ->setReferenceVersion($row['reference']['refVersion'])
                ;

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
                    ->setContentChannels(
                        !empty($properties['content_channels']) ? $properties['content_channels'] : null
                    );

                $elementtypeStructure->addNode($node);

                if (!empty($row['children'])) {
                    $sort = $this->iterateData($elementtypeStructure, $rootNode, $user, $sort, $row['children']);
                }
            }
        }

        return $sort;
    }
}
