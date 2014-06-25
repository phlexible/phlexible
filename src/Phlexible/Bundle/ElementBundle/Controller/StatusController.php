<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/elements")
 * @Security("is_granted('debug')")
 */
class StatusController extends Controller
{
    /**
     * List all Elements
     *
     * @return Response
     * @Route("", name="elements_status")
     */
    public function indexAction()
    {
        $out = '<pre>';

        $db = $this->getContainer()->dbPool->read;

        // elements

        $elementSelect = $db->select()
            ->from($db->prefix . 'element', new Zend_Db_Expr('COUNT(*)'));
        $numElements = $db->fetchOne($elementSelect);

        $elementTreeSelect = $db->select()
            ->distinct()
            ->from(array('et' => $db->prefix . 'element_tree'), array('eid'))
            ->join(array('e' => $db->prefix . 'element'), 'et.eid = e.eid', array());
        $numTreeElements = count($db->fetchCol($elementTreeSelect));

        $elementTeaserSelect = $db->select()
            ->distinct()
            ->from(array('ett' => $db->prefix . 'element_tree_teasers'), array('teaser_eid'))
            ->join(array('e' => $db->prefix . 'element'), 'ett.teaser_eid = e.eid', array());
        $numTeaserElements = count($db->fetchCol($elementTeaserSelect));

        $elementUnusedSelect = $db->select()
            ->distinct()
            ->from($db->prefix . 'element', new Zend_Db_Expr('COUNT(*)'))
            ->where('eid NOT IN (SELECT DISTINCT eid FROM '.$db->prefix.'element_tree)')
            ->where('eid NOT IN (SELECT DISTINCT teaser_eid FROM '.$db->prefix.'element_tree_teasers WHERE teaser_eid IS NOT NULL)');
        $numUnusedElements = $db->fetchOne($elementUnusedSelect);

        $elementVersionsSelect = $db->select()
            ->from($db->prefix . 'element_version', new Zend_Db_Expr('COUNT(*)'));
        $numElementVersions = $db->fetchOne($elementVersionsSelect);

        $elementGroupedVersionsSelect = $db->select()
            ->from($db->prefix . 'element_version', array('eid', new Zend_Db_Expr('COUNT(*) AS cnt')))
            ->group('eid')
            ->order('cnt DESC');
        $numGroupedElementVersions = $db->fetchPairs($elementGroupedVersionsSelect);

        // elementtypes

        $elementTypeSelect = $db->select()
            ->from($db->prefix . 'elementtype', new Zend_Db_Expr('COUNT(*)'));
        $numElementTypes = $db->fetchOne($elementTypeSelect);

        $elementTypeUnusedSelect = $db->select()
            ->from($db->prefix . 'elementtype', new Zend_Db_Expr('COUNT(*)'))
            ->where('element_type_id NOT IN (SELECT DISTINCT element_type_id FROM ' . $db->prefix . 'element)')
            ->where('element_type_id NOT IN (SELECT DISTINCT reference_id FROM ' . $db->prefix . 'elementtype_structure)');
        $numUnusedElementTypes = $db->fetchOne($elementTypeUnusedSelect);

        $elementTypeVersionsSelect = $db->select()
            ->from($db->prefix . 'elementtype_version', new Zend_Db_Expr('COUNT(*)'));
        $numElementTypeVersions = $db->fetchOne($elementTypeVersionsSelect);

        $elementTypeGroupedVersionsSelect = $db->select()
            ->from($db->prefix . 'elementtype_version', array('element_type_id', new Zend_Db_Expr('COUNT(*) AS cnt')))
            ->group('element_type_id')
            ->order('cnt DESC');
        $numGroupedElementTypeVersions = $db->fetchPairs($elementTypeGroupedVersionsSelect);

        $elementTypeGroupedElementsSelect = $db->select()
            ->from(array('et' => $db->prefix . 'elementtype'), array('element_type_id', new Zend_Db_Expr('COUNT(e.eid) AS cnt')))
            ->joinLeft(array('e' => $db->prefix . 'element'), 'e.element_type_id = et.element_type_id', array())
            ->group('element_type_id')
            ->order('cnt DESC');
        $numGroupedElementTypeElements = $db->fetchPairs($elementTypeGroupedElementsSelect);

        $out .= '##### Elements ############################################' . PHP_EOL;
        $out .= PHP_EOL;
        $out .= $numElements . ' elements' . PHP_EOL;
        $out .= PHP_EOL;
        $out .= $numTreeElements . ' tree elements' . PHP_EOL;
        $out .= $numTeaserElements . ' teaser elements' . PHP_EOL;
        $out .= PHP_EOL;
        $out .= ($numTreeElements + $numTeaserElements) . ' used elements' . PHP_EOL;
        $out .= $numUnusedElements . ' unused elements' . PHP_EOL;
        $out .= '(= ' . ($numTreeElements + $numTeaserElements + $numUnusedElements) . ' elements)' . PHP_EOL;
        $out .= PHP_EOL;
        $out .= $numElementVersions . ' element versions' . PHP_EOL;
        $out .= PHP_EOL;
        $out .= 'Top 5 most element versions:' . PHP_EOL;
        $i = 0;
        foreach ($numGroupedElementVersions as $eid => $count) {
            if ($i >= 5) break;
            $element = Makeweb_Elements_Element_Version_Manager::getInstance()->getLatest($eid);
            $out .= '  ' . str_pad($element->getBackendTitle('de', 'en') . ' [' . $eid . ']', 40, ' ') . ' => ' . str_pad($count, 4, ' ', STR_PAD_LEFT) . ' versions' . PHP_EOL;
            $i++;
        }
        $out .= PHP_EOL;
        $out .= '##### Element Types #######################################' . PHP_EOL;
        $out .= PHP_EOL;
        $out .= $numElementTypes . ' element types' . PHP_EOL;
        $out .= $numUnusedElementTypes . ' unused element types' . PHP_EOL;
        $out .= PHP_EOL;
        $out .= $numElementTypeVersions . ' element type versions' . PHP_EOL;
        $out .= PHP_EOL;
        $out .= 'Top 5 most element type versions:' . PHP_EOL;
        $i = 0;
        foreach ($numGroupedElementTypeVersions as $etId => $count) {
            if ($i >= 5) break;
            $elementType = Makeweb_Elementtypes_Elementtype_Version_Manager::getInstance()->getLatest($etId);
            $out .= '  ' . str_pad($elementType->getTitle() . ' [' . $etId . ']', 40, ' ') . ' => ' . str_pad($count, 4, ' ', STR_PAD_LEFT) . ' versions' . PHP_EOL;
            $i++;
        }
        $out .= PHP_EOL;
        $out .= 'Top 5 most element types by elements:' . PHP_EOL;
        $i = 0;
        foreach ($numGroupedElementTypeElements as $etId => $count) {
            if ($i >= 5) break;
            $elementType = Makeweb_Elementtypes_Elementtype_Version_Manager::getInstance()->getLatest($etId);
            $out .= '  ' . str_pad($elementType->getTitle() . ' [' . $etId . ']', 40, ' ') . ' => ' . str_pad($count, 4, ' ', STR_PAD_LEFT) . ' elements' . PHP_EOL;
            $i++;
        }

        return new Response($out);
    }
}
