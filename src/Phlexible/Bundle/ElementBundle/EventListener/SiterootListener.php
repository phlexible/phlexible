<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\SiterootBundle\Event\SiterootEvent;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;

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
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var string
     */
    private $masterLanguage;

    /**
     * @param ElementService       $elementService
     * @param ElementtypeService   $elementtypeService
     * @param TreeManager          $treeManager
     * @param UserManagerInterface $userManager
     * @param string               $masterLanguage
     */
    public function __construct(
        ElementService $elementService,
        ElementtypeService $elementtypeService,
        TreeManager $treeManager,
        UserManagerInterface $userManager,
        $masterLanguage)
    {
        $this->elementService = $elementService;
        $this->elementtypeService = $elementtypeService;
        $this->treeManager = $treeManager;
        $this->userManager = $userManager;
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
            ->setLabels(['fieldLabel' => ['de' => 'Daten', 'en' => 'Data']])
            ->setConfiguration([])
            ->setContentChannels([])
            ->setValidation([]);

        $textfield = new ElementtypeStructureNode();
        $textfield
            ->setParentNode($tab)
            ->setParentDsId($tab->getDsId())
            ->setDsId(Uuid::generate())
            ->setName('title')
            ->setType('textfield')
            ->setLabels(['fieldLabel' => ['de' => 'Titel', 'en' => 'Title']])
            ->setConfiguration(['required' => 'always'])
            ->setContentChannels([])
            ->setValidation([]);

        $elementtypeStructure
            ->addNode($root)
            ->addNode($tab)
            ->addNode($textfield);

        $mappings = [
            'backend' => [
                'fields' => [
                    ['ds_id' => $textfield->getDsId(), 'field' => 'Title', 'index' => 1]
                ],
                'pattern' => '$1'
            ]
        ];

        $user = $this->userManager->find($siteroot->getModifyUserId());

        $elementtype = $this->elementtypeService->createElementtype(
            'structure',
            'site_root_' . $siteroot->getId(),
            'Site root ' . $siteroot->getTitle(),
            'www_root.gif',
            $elementtypeStructure,
            $mappings,
            $user->getUsername(),
            false
        );

        $elementSource = $this->elementService->createElementSource($elementtype);

        $element = $this->elementService->createElement($elementSource, $this->masterLanguage, $siteroot->getModifyUserId());

        $tree = $this->treeManager->getBySiteRootId($siteroot->getId());
        $tree->init('element', $element->getEid(), $element->getCreateUserId());
    }
}
