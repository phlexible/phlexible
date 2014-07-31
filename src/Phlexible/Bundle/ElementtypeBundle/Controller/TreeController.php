<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Phlexible\Bundle\ElementtypeBundle\ElementtypesMessage;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeStructure\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Entity\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tree controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/tree")
 * @Security("is_granted('elementtypes')")
 */
class TreeController extends Controller
{
    /**
     * Return an Element Type Data Tree
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="elementtypes_tree")
     */
    public function indexAction(Request $request)
    {
        $id = $request->get('id', null);
        $version = $request->get('version', null);
        $mode = $request->get('mode', 'edit');

        $elementtypeService = $this->get('phlexible_elementtype.service');
        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');

        $elementtype = $elementtypeService->findElementtype($id);
        $elementtypeVersion = $elementtypeService->findElementtypeVersion($elementtype, $version);
        $elementtypeStructure = $elementtypeService->findElementtypeStructure($elementtypeVersion);

        $rootNode = $elementtypeStructure->getRootNode();
        $type = $elementtype->getType(); // != 'reference' ? 'root' : 'referenceroot';

        $metaSetId = $elementtypeVersion->getMetaSetId();
        $allMetaSets = $metaSetManager->findAll();

        $metaSets = array();
        foreach ($allMetaSets as $metaSet) {
            $metaSets[] = array($metaSet->getId(), $metaSet->getName());
        }

        $children = array();
        $rootID = '';
        $rootDsId = '';
        $rootType = 'root';
        if ($rootNode) {
            $rootID = $rootNode->getId();
            $rootDsId = $rootNode->getDsId();
            $rootType = $rootNode->getType();
            $children = $elementtypeStructure->getChildNodes($rootNode->getDsId());
        }

        $language = $this->getUser()->getInterfaceLanguage();
        if (!$language) {
            $language = 'en';
        }

        $data = array(
            array(
                'text'                 => $elementtype->getTitle() .
                    ' [v' . $elementtypeVersion->getVersion() . ', ' .
                    $elementtype->getType() . ']',
                'id'                   => $rootID,
                'ds_id'                => $rootDsId,
                'element_type_id'      => $elementtype->getId(),
                'element_type_version' => $elementtypeVersion->getVersion(),
                'icon'                 => '/bundles/phlexibleelementtype/elementtypes/' . $elementtype->getIcon(),
                'cls'                  => 'p-elementtypes-type-' . $type,
                'leaf'                 => false,
                'expanded'             => true,
                'type'                 => $rootType,
                'allowDrag'            => ($type == Elementtype::TYPE_REFERENCE),
                'allowDrop'            => $mode == 'edit',
                'editable'             => $mode == 'edit',
                'properties'           => array(
                    'root'     => array(
                        'title'               => $elementtype->getTitle(),
                        'reference_title'     => $elementtype->getTitle() .
                            ' [v' . $elementtypeVersion->getVersion() . ']',
                        'unique_id'           => $elementtype->getUniqueID(),
                        'icon'                => $elementtype->getIcon(),
                        'hide_children'       => $elementtype->getHideChildren() ? 'on' : '',
                        'default_tab'         => $elementtype->getDefaultTab(),
                        'default_content_tab' => $elementtypeVersion->getDefaultContentTab(),
                        'type'                => $type,
                        'metaset'             => $metaSetId,
                        'comment'             => $elementtypeVersion->getComment(),
                    ),
                    'mappings' => $elementtypeVersion->getMappings(),
                    'metasets' => $metaSets,
                ),
                'children'             => $this->recurseTree(
                    $elementtypeStructure,
                    $children,
                    $language,
                    $mode,
                    false,
                    true
                )
            )
        );

        return new JsonResponse($data);
    }

    /**
     * Build an Element Type data tree
     *
     * @param ElementtypeStructure       $structure
     * @param ElementtypeStructureNode[] $nodes
     * @param string                     $language
     * @param string                     $mode
     * @param bool                       $reference
     * @param bool                       $allowDrag
     *
     * @return array
     */
    private function recurseTree(
        ElementtypeStructure $structure,
        array $nodes,
        $language,
        $mode = 'edit',
        $reference = false,
        $allowDrag = true)
    {
        $return = array();

        foreach ($nodes as $node) {
            /* @var $node ElementtypeStructureNode */

            $tmp = array(
                'text'       => $node->getLabel('fieldlabel', $language) . ' (' . $node->getName() . ')',
                'id'         => $node->getId(),
                'ds_id'      => $node->getDsId(),
                'cls'        => 'p-elementtypes-node p-elementtypes-type-' . $node->getType(
                    ) . ($reference ? ' p-elementtypes-reference' : ''),
                'leaf'       => true,
                'expanded'   => false,
                'type'       => $node->getType(),
                'iconCls'    => $this->get('phlexible_elementtype.field.registry')->getField(
                        $node->getType()
                    )->getIcon(),
                'reference'  => $reference,
                'allowDrag'  => $allowDrag,
                'allowDrop'  => $mode == 'edit' && !$reference,
                'editable'   => $mode == 'edit' || !$reference,
                'properties' => array(
                    'field'            => array(
                        'title'         => $node->getName(),
                        'type'          => $node->getType(),
                        'working_title' => $node->getName(),
                        'comment'       => $node->getComment(),
                        'image'         => ''
                    ),
                    'configuration'    => $node->getConfiguration(),
                    'labels'           => $node->getLabels(),
                    'options'          => $node->getOptions(),
                    'validation'       => $node->getValidation(),
                    'content_channels' => $node->getContentChannels(),
                )
            );

            if ($structure->hasChildNodes($node->getDsId())) {
                $tmp['leaf'] = false;
                $tmp['expanded'] = true;
                $tmp['children'] = $this->recurseTree(
                    $structure,
                    $structure->getChildNodes($node->getDsId()),
                    $language,
                    $mode,
                    $reference
                );
            }

            if ($node->isReference()) {
                $elementtypesService = $this->get('phlexible_elementtype.service');

                $referenceId = $node->getReferenceElementtype()->getId();
                $referenceVersion = $node->getReferenceVersion();
                $elementtype = $elementtypesService->findElementtype($referenceId);
                $elementtypeVersion = $elementtypesService->findElementtypeVersion($elementtype, $referenceVersion);
                $children = $structure->getChildNodes($node->getDsId());
                $referenceRoot = $children[0];

                $tmp['text'] = $elementtype->getTitle() . ' [v' . $elementtypeVersion->getVersion() . ']';
                $tmp['leaf'] = false;
                $tmp['expanded'] = true;
                $tmp['reference'] = array('refID' => $referenceId, 'refVersion' => $referenceVersion);
                $tmp['editable'] = false;
                $tmp['allowDrag'] = true;
                $tmp['children'] = $this->recurseTree(
                    $structure,
                    $structure->getChildNodes($referenceRoot->getDsId()),
                    $language,
                    'template',
                    true,
                    true
                );
                //                $tmp['cls'] = 'p-elementtypes-type-reference';
            }

            $return[] = $tmp;
        }

        return $return;
    }

    /**
     * Save an Element Type data tree
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="elementtypes_tree_save")
     */
    public function saveAction(Request $request)
    {
        $elementtypeId = $request->get('element_type_id', false);
        $data = json_decode($request->get('data'), true);

        if (!$elementtypeId) {
            return new ResultResponse(false, 'No elementtype ID.');
        }

        $rootRow = array_shift($data);
        $rootType = $rootRow['type'];

        if ($rootType != 'root' && $rootType != 'referenceroot') {
            return new ResultResponse(false, 'Invalid root node.');
        }

        $rootProperties = $rootRow['properties'];

        if (!isset($rootProperties['root']['unique_id']) || !trim($rootProperties['root']['unique_id'])) {
            return new ResultResponse(false, 'No unique ID.');
        }

        $elementtypeService = $this->get('phlexible_elementtype.service');

        $elementtype = $elementtypeService->findElementtype($elementtypeId);
        $priorElementtypeVersion = $elementtypeService->findLatestElementtypeVersion($elementtype);

        $elementtypeVersion = clone $priorElementtypeVersion;
        $elementtypeVersion
            ->setVersion($elementtypeVersion->getVersion() + 1)
            ->setDefaultContentTab(
                strlen(
                    $rootProperties['root']['default_content_tab']
                ) ? $rootProperties['root']['default_content_tab'] : null
            )
            ->setMappings(!empty($rootProperties['mappings']) ? $rootProperties['mappings'] : null)
            ->setComment(trim($rootProperties['root']['comment']))
            ->setCreateUserId($this->getUser()->getId())
            ->setCreatedAt(new \DateTime());

        $elementtypeStructure = new ElementtypeStructure();
        $elementtypeStructure
            ->setElementTypeVersion($elementtypeVersion);

        $rootNode = new ElementtypeStructureNode();
        $rootDsId = !empty($rootRow['ds_id']) ? $rootRow['ds_id'] : Uuid::generate();
        $rootNode
            ->setElementtypeStructure($elementtypeStructure)
            ->setDsId($rootDsId)
            ->setType($rootType);

        $elementtypeStructure->addNode($rootNode);

        foreach ($data as $row) {
            if (!$row['parent_ds_id']) {
                $row['parent_ds_id'] = $rootDsId;
            }
            $node = new ElementtypeStructureNode();
            $parentNode = $elementtypeStructure->getNode($row['parent_ds_id']);

            $node
                ->setElementtypeStructure($elementtypeStructure)
                ->setDsId(!empty($row['ds_id']) ? $row['ds_id'] : Uuid::generate())
                ->setParentDsId($parentNode->getDsId());

            if ($row['type'] == 'reference') {
                $node
                    ->setType('reference')
                    ->setReferenceId($row['reference']['refID'])
                    ->setReferenceVersion($row['reference']['refVersion']);
            } else {
                $properties = $row['properties'];

                $node
                    ->setType($properties['field']['type'])
                    ->setName(trim($properties['field']['working_title']))
                    ->setComment(trim($properties['field']['comment']))
                    ->setConfiguration(!empty($properties['configuration']) ? $properties['configuration'] : null)
                    ->setValidation(!empty($properties['validation']) ? $properties['validation'] : null)
                    ->setLabels(!empty($properties['labels']) ? $properties['labels'] : null)
                    ->setOptions(!empty($properties['options']) ? $properties['options'] : null)
                    ->setContentChannels(
                        !empty($properties['content_channels']) ? $properties['content_channels'] : null
                    );
            }

            $elementtypeStructure->addNode($node);
        }

        $db = $this->getContainer()->get('connection_manager')->default;
        $db->beginTransaction();

        $elementtypeService->createElementtypeVersion(
            $elementtypeStructure,
            trim($rootProperties['root']['unique_id']),
            trim($rootProperties['root']['title']),
            trim($rootProperties['root']['icon']),
            !empty($rootProperties['root']['hide_children']),
            strlen($rootProperties['root']['default_tab']) ? $rootProperties['root']['default_tab'] : null
        );

        /*
        // update elementtypes that use this elementtype as reference

        $updateData = array(
            'reference_version' => $version,
        );
        $db->update($db->prefix.'elementtype_structure', $updateData, 'reference_id = '.$db->quote($elementtypeId));

        $select = $db->select()
                     ->distinct()
                     ->from($db->prefix . 'elementtype_structure', array('id', 'element_type_id', 'version'))
                     ->where('reference_id = ?', $elementtypeId);

        $candidates = $db->fetchAll($select);

        $select = $db->select()
                     ->from($db->prefix . 'elementtype_version', new Zend_Db_Expr('MAX(version)'))
                     ->where('element_type_id = ?');

        foreach ($candidates as $row)
        {
            $maxVersion = $db->fetchOne($select, $row['element_type_id']);

            if ($row['version'] != $maxVersion)
            {
                continue;
            }

            $newElementVersion = $manager->copyVersion($row['element_type_id'], $row['version'], null, null, true);

            $db->update($db->prefix . 'elementtype_structure', array(
                'reference_version' => $version,
            ), 'reference_id = '.$db->quote($elementtypeId).' AND
                element_type_id = '.$db->quote($row['element_type_id']).' AND
                version = '.$db->quote($newElementVersion));
        }
        */

        $db->commit();

        // post message
        $message = ElementtypesMessage::create(
            "Element Type {$elementtype->getTitle()} saved in version {$elementtypeVersion->getVersion()}"
        );
        $this->get('phlexible_message.message_poster')->post($message);

        return new ResultResponse(
            true,
            $elementtypeId,
            'Element Type "' . $elementtype->getTitle() . '" saved as Version ' . $elementtypeVersion->getVersion(
            ) . '.'
        );
    }

    /**
     * Transform an Element Type node into a reference
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/transform", name="elementtypes_tree_transform")
     */
    public function transformAction(Request $request)
    {
        $data = json_decode($request->get('data'), true);

        $title = $data[0]['properties']['labels']['fieldlabel']['de'];

        $elementtypeService = $this->get('phlexible_elementtype.service');

        $referenceElementtype = $elementtypeService->create(
            Elementtype::TYPE_REFERENCE,
            $title,
            $title,
            null,
            $this->getUser()->getId()
        );

        $referenceElementtypeVersion = $elementtypeService->findLatestElementtypeVersion($referenceElementtype);

        /*
        $newElementType = new Makeweb_Elementtypes_Elementtype();
        $newElementType->setType('reference');
        $elementTypeID = $newElementType->save();

        $newElementType->createInitialVersion($title);

        $insertData = array(
            'element_type_id' => $elementTypeID,
            'version'         => $version,
            'title'           => trim($title),
            'type'            => 'reference',
            'icon'            => '',
            'comment'         => 'Created by blabla',
            'modify_uid'      => MWF_Env::getUid(),
            'modify_time'     => $db->fn->now()
        );

        $db->insert($db->prefix.'elementtype_version', $insertData);
        */

        $referenceRootNode = new ElementtypeStructureNode();
        $referenceRootNode
            ->setElementtypeStructure($referenceElementtypeVersion)
            ->setComment('Transformed to reference.')
            ->setDsId(Uuid::generate())
            ->setType('referenceroot');

        $referenceStructure = new ElementtypeStructure();
        $referenceStructure
            ->setElementTypeVersion($referenceElementtypeVersion)
            ->addNode($referenceRootNode);

        foreach ($data as $row) {
            $node = new ElementtypeStructureNode();
            $parentNode = $referenceStructure->getNode($row['parent_ds_id']);

            $node
                ->setDsId(!empty($row['ds_id']) ? $row['ds_id'] : Uuid::generate())
                ->setParentDsId($parentNode->getDsId())
                ->setElementtypeStructure($referenceElementtypeVersion->getVersion());

            if ($row['type'] == 'reference') {
                $node
                    ->setType('reference')
                    ->setReferenceId($row['reference']['refID'])
                    ->setReferenceVersion($row['reference']['refVersion']);
            } else {
                $properties = $row['properties'];

                $node
                    ->setType($properties['field']['type'])
                    ->setName(trim($properties['field']['working_title']))
                    ->setComment(trim($properties['field']['comment']))
                    ->setConfiguration(!empty($properties['configuration']) ? $properties['configuration'] : array())
                    ->setValidation(!empty($properties['validation']) ? $properties['validation'] : array())
                    ->setLabels(!empty($properties['labels']) ? $properties['labels'] : array())
                    ->setOptions(!empty($properties['options']) ? $properties['options'] : array())
                    ->setContentChannels(
                        !empty($properties['content_channels']) ? $properties['content_channels'] : array()
                    );
            }

            $referenceStructure->addNode($node);
        }

        print_r($referenceStructure);
        die;

        return new JsoNResponse(
            true,
            $referenceElementtypeVersion->getVersion(),
            'Reference Element Type "' . $title . '" created.',
            array(
                'title'                => $title . ' [v' . $referenceElementtypeVersion->getVersion() . ']',
                'element_type_id'      => $referenceElementtype->getId(),
                'element_type_version' => $referenceElementtypeVersion->getVersion(),
            )
        );
    }
}
