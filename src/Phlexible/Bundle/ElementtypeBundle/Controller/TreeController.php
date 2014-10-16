<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
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
        $id = $request->get('id');
        $version = $request->get('version');
        $mode = $request->get('mode', 'edit');

        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');

        $elementtype = $elementtypeService->findElementtype($id);
        $elementtypeStructure = $elementtype->getStructure();

        $rootNode = $elementtypeStructure->getRootNode();
        $type = $elementtype->getType(); // != 'reference' ? 'root' : 'referenceroot';

        $children = array();
        $rootDsId = '';
        $rootType = 'root';
        if ($rootNode) {
            $rootDsId = $rootNode->getDsId();
            $rootType = $rootNode->getType();
            $children = $elementtypeStructure->getChildNodes($rootNode->getDsId());
        }

        $language = $this->getUser()->getInterfaceLanguage('en');
        if (!$language) {
            $language = 'en';
        }

        $data = array(
            array(
                'text'                 => $elementtype->getTitle($language)
                    . ' [v' . $elementtype->getRevision() . ', '
                    . $elementtype->getType() . ']',
                'id'                   => md5(serialize($rootNode)),
                'ds_id'                => $rootDsId,
                'element_type_id'      => $elementtype->getId(),
                'element_type_version' => $elementtype->getRevision(),
                'icon'                 => '/bundles/phlexibleelementtype/elementtypes/' . $elementtype->getIcon(),
                'cls'                  => 'p-elementtypes-type-' . $type,
                'leaf'                 => false,
                'expanded'             => true,
                'type'                 => $rootType,
                'allowDrag'            => ($type == Elementtype::TYPE_REFERENCE),
                'allowDrop'            => $mode == 'edit',
                'editable'             => $mode == 'edit',
                'properties'           => array(
                    'root' => array(
                        'title'               => $elementtype->getTitle($language),
                        'reference_title'     => $elementtype->getTitle($language)
                            . ' [v' . $elementtype->getRevision() . ']',
                        'unique_id'           => $elementtype->getUniqueId(),
                        'icon'                => $elementtype->getIcon(),
                        'hide_children'       => $elementtype->getHideChildren() ? 'on' : '',
                        'default_tab'         => $elementtype->getDefaultTab(),
                        'default_content_tab' => $elementtype->getDefaultContentTab(),
                        'type'                => $type,
                        'metaset'             => $elementtype->getMetaSetId(),
                        'comment'             => $elementtype->getComment(),
                    ),
                    'mappings' => $elementtype->getMappings()
                ),
                'children' => $this->recurseTree(
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

        $fieldRegistry = $this->get('phlexible_elementtype.field.registry');

        foreach ($nodes as $node) {
            /* @var $node ElementtypeStructureNode */

            $tmp = array(
                'text'       => $node->getLabel('fieldLabel', $language) . ' (' . $node->getName() . ')',
                'id'         => md5(serialize($node)),
                'ds_id'      => $node->getDsId(),
                'cls'        => 'p-elementtypes-node p-elementtypes-type-' . $node->getType()
                    . ($reference ? ' p-elementtypes-reference' : ''),
                'leaf'       => true,
                'expanded'   => false,
                'type'       => $node->getType(),
                'iconCls'    => $fieldRegistry->getField($node->getType())->getIcon(),
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
                $elementtypesService = $this->get('phlexible_elementtype.elementtype_service');

                $referenceElementtype = $elementtypesService->findElementtype($node->getReferenceElementtypeId());
                $children = $structure->getChildNodes($node->getDsId());
                $referenceRoot = $children[0];

                $tmp['text'] = $referenceElementtype->getTitle($language) . ' [v' . $referenceElementtype->getRevision() . ']';
                $tmp['leaf'] = false;
                $tmp['expanded'] = true;
                $tmp['reference'] = array('refID' => $referenceElementtype->getId(), 'refVersion' => $referenceElementtype->getRevision());
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
        $treeSaver = $this->get('phlexible_elementtype.tree_saver');

        $elementtype = $treeSaver->save($request, $this->getUser());

        return new ResultResponse(
            true,
            "Element Type {$elementtype->getTitle()} saved as version {$elementtype->getRevision()}."
        );
    }
}
