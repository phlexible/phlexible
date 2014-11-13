<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Doctrine\DBAL\Connection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/elements")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
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

        $elementService = $this->get('phlexible_element.element_service');
        $elementtypeService = $this->get('phlexible_elementtype.elementtype_service');
        $conn = $this->get('doctrine.dbal.default_connection');
        /* @var $conn Connection */

        // elements

        $qb = $conn->createQueryBuilder()
            ->select('COUNT(e.eid)')
            ->from('element', 'e');
        $numElements = $conn->fetchColumn($qb->getSQL());

        $qb = $conn->createQueryBuilder()
            ->select('DISTINCT t.type_id')
            ->from('tree', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->where('t.type = "element"');
        $numTreeElements = count($conn->fetchAll($qb->getSQL()));

        $qb = $conn->createQueryBuilder()
            ->select('DISTINCT t.type_id')
            ->from('teaser', 't')
            ->join('t', 'element', 'e', 't.type_id = e.eid')
            ->where('t.type = "element"');
        $numTeaserElements = count($conn->fetchAll($qb->getSQL()));

        $qb = $conn->createQueryBuilder()
            ->select('COUNT(e.eid)')
            ->from('element', 'e')
            ->where('eid NOT IN (SELECT DISTINCT type_id FROM tree WHERE type = "element")')
            ->andWhere('eid NOT IN (SELECT DISTINCT type_id FROM teaser WHERE type = "element")');
        $numUnusedElements = $conn->fetchColumn($qb->getSQL());

        $qb = $conn->createQueryBuilder()
            ->select('COUNT(ev.id)')
            ->from('element_version', 'ev');
        $numElementVersions = $conn->fetchColumn($qb->getSQL());

        $qb = $conn->createQueryBuilder()
            ->select(['ev.eid, COUNT(ev.eid) AS cnt'])
            ->from('element_version', 'ev')
            ->groupBy('ev.eid')
            ->orderBy('cnt', 'DESC');
        $numGroupedElementVersions = [];
        foreach ($conn->fetchAll($qb->getSQL()) as $row) {
            $numGroupedElementVersions[$row['eid']] = $row['cnt'];
        }

        // elementtypes

        $qb = $conn->createQueryBuilder()
            ->select('COUNT(et.id)')
            ->from('elementtype', 'et');
        $numElementTypes = $conn->fetchColumn($qb->getSQL());

        $qb = $conn->createQueryBuilder()
            ->select('COUNT(et.id)')
            ->from('elementtype', 'et')
            ->where('et.id NOT IN (SELECT DISTINCT element_type_id FROM element)')
            ->where('et.id NOT IN (SELECT DISTINCT reference_id FROM elementtype_structure)');
        $numUnusedElementTypes = $conn->fetchColumn($qb);

        $qb = $conn->createQueryBuilder()
            ->select('COUNT(etv.id)')
            ->from('elementtype_version', 'etv');
        $numElementTypeVersions = $conn->fetchColumn($qb->getSQL());

        $qb = $conn->createQueryBuilder()
            ->select(['etv.elementtype_id', 'COUNT(etv.id) AS cnt'])
            ->from('elementtype_version', 'etv')
            ->groupBy('etv.elementtype_id')
            ->orderBy('cnt', 'DESC');
        $numGroupedElementTypeVersions = [];
        foreach ($conn->fetchAll($qb->getSQL()) as $row) {
            $numGroupedElementTypeVersions[$row['elementtype_id']] = $row['cnt'];
        }

        $qb = $conn->createQueryBuilder()
            ->select(['et.id', 'COUNT(e.eid) AS cnt'])
            ->from('elementtype', 'et')
            ->leftJoin('et', 'element', 'e', 'e.elementtype_id = et.id')
            ->groupBy('et.id')
            ->orderBy('cnt', 'DESC');
        $numGroupedElementTypeElements = [];
        foreach ($conn->fetchAll($qb->getSQL()) as $row) {
            $numGroupedElementTypeElements[$row['id']] = $row['cnt'];
        }

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
            if ($i >= 5) {
                break;
            }
            $element = $elementService->findElement($eid);
            $elementVersion = $elementService->findLatestElementVersion($element);
            $out .= '  ' . str_pad($elementVersion->getBackendTitle('de', 'en') . ' [' . $eid . ']', 40, ' ')
                . ' => ' . str_pad($count, 4, ' ', STR_PAD_LEFT) . ' versions' . PHP_EOL;
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
            if ($i >= 5) {
                break;
            }
            $elementtype = $elementtypeService->findElementtype($etId);
            $out .= '  ' . str_pad($elementtype->getTitle() . ' [' . $etId . ']', 40, ' ')
                . ' => ' . str_pad($count, 4, ' ', STR_PAD_LEFT) . ' versions' . PHP_EOL;
            $i++;
        }
        $out .= PHP_EOL;
        $out .= 'Top 5 most element types by elements:' . PHP_EOL;
        $i = 0;
        foreach ($numGroupedElementTypeElements as $etId => $count) {
            if ($i >= 5) {
                break;
            }
            $elementtype = $elementtypeService->findElementtype($etId);
            $out .= '  ' . str_pad($elementtype->getTitle() . ' [' . $etId . ']', 40, ' ') . ' => '
                . str_pad($count, 4, ' ', STR_PAD_LEFT) . ' elements' . PHP_EOL;
            $i++;
        }

        return new Response($out);
    }
}
