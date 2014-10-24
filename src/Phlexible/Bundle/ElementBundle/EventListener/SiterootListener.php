<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\ElementVersion\FieldMapper;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\SiterootBundle\Event\SiterootEvent;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Siteroot listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootListener
{
    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementtypeService
     */
    private $elementtypeService;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @var string
     */
    private $masterLanguage;

    /**
     * @param ElementService           $elementService
     * @param ElementtypeService       $elementtypeService
     * @param TreeManager              $treeManager
     * @param SecurityContextInterface $securityContext
     * @param string                   $masterLanguage
     */
    public function __construct(
        ElementService $elementService,
        ElementtypeService $elementtypeService,
        TreeManager $treeManager,
        SecurityContextInterface $securityContext,
        $masterLanguage)
    {
        $this->elementService = $elementService;
        $this->elementtypeService = $elementtypeService;
        $this->treeManager = $treeManager;
        $this->securityContext = $securityContext;
        $this->masterLanguage = $masterLanguage;
    }

    /**
     * @param SiterootEvent $event
     */
    public function onCreateSiteroot(SiterootEvent $event)
    {
        $siteroot = $event->getSiteroot();

        $elementtypeStructure = new ElementtypeStructure();

        $root = new ElementtypeStructureNode();
        $root
            ->setDsId(Uuid::generate())
            ->setName('root')
            ->setType('root');

        $tab = new ElementtypeStructureNode();
        $tab
            ->setParentNode($root)
            ->setParentDsId($root->getDsId())
            ->setDsId(Uuid::generate())
            ->setName('data')
            ->setType('tab')
            ->setLabels(array('fieldLabel' => array('de' => 'Daten', 'en' => 'Data')))
            ->setConfiguration(array())
            ->setContentChannels(array())
            ->setValidation(array());

        $textfield = new ElementtypeStructureNode();
        $textfield
            ->setParentNode($tab)
            ->setParentDsId($tab->getDsId())
            ->setDsId(Uuid::generate())
            ->setName('title')
            ->setType('textfield')
            ->setLabels(array('fieldLabel' => array('de' => 'Titel', 'en' => 'Title')))
            ->setConfiguration(array('required' => 'always'))
            ->setContentChannels(array())
            ->setValidation(array());

        $elementtypeStructure
            ->addNode($root)
            ->addNode($tab)
            ->addNode($textfield);

        $mappings = array(
            'backend' => array(
                'fields' => array(
                    array('ds_id' => $textfield->getDsId(), 'field' => 'Title', 'index' => 1)
                ),
                'pattern' => '$1'
            )
        );

        $elementtype = $this->elementtypeService->createElementtype(
            'structure',
            'site_root_' . $siteroot->getId(),
            'Site root ' . $siteroot->getTitle(),
            'www_root.gif',
            $elementtypeStructure,
            $mappings,
            $siteroot->getModifyUserId(),
            false
        );

        $elementSource = $this->elementService->createElementSource($elementtype);

        $element = $this->elementService->createElement($elementSource, $this->masterLanguage, $siteroot->getModifyUserId());

        $tree = $this->treeManager->getBySiteRootId($siteroot->getId());
        $tree->init('element', $element->getEid(), $element->getCreateUserId());
    }
}
