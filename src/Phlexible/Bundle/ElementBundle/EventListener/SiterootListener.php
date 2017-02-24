<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeService;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructureNode;
use Phlexible\Bundle\SiterootBundle\Event\SiterootEvent;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Phlexible\Bundle\UserBundle\Model\UserManagerInterface;
use Phlexible\Component\Util\UuidUtil;

/**
 * Siteroot listener.
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
            ->setDsId(UuidUtil::generate())
            ->setName('root')
            ->setType('root');

        $tab = new ElementtypeStructureNode();
        $tab
            ->setParentNode($root)
            ->setParentDsId($root->getDsId())
            ->setDsId(UuidUtil::generate())
            ->setName('data')
            ->setType('tab')
            ->setLabels(['fieldLabel' => ['de' => 'Daten', 'en' => 'Data']])
            ->setConfiguration([])
            ->setValidation([]);

        $textfield = new ElementtypeStructureNode();
        $textfield
            ->setParentNode($tab)
            ->setParentDsId($tab->getDsId())
            ->setDsId(UuidUtil::generate())
            ->setName('title')
            ->setType('textfield')
            ->setLabels(['fieldLabel' => ['de' => 'Titel', 'en' => 'Title']])
            ->setConfiguration(['required' => 'always'])
            ->setValidation([]);

        $elementtypeStructure
            ->addNode($root)
            ->addNode($tab)
            ->addNode($textfield);

        $mappings = [
            'backend' => [
                'fields' => [
                    ['ds_id' => $textfield->getDsId(), 'field' => 'Title', 'index' => 1],
                ],
                'pattern' => '$1',
            ],
        ];

        $user = $this->userManager->find($siteroot->getModifyUserId());

        $elementtype = $this->elementtypeService->createElementtype(
            'structure',
            'site_root_'.$siteroot->getId(),
            'Site root '.$siteroot->getTitle(),
            'www_root.gif',
            $elementtypeStructure,
            $mappings,
            $user->getUsername(),
            false
        );

        $elementSource = $this->elementService->createElementSource($elementtype);

        $element = $this->elementService->createElement($elementSource, $this->masterLanguage, $siteroot->getModifyUserId());

        $tree = $this->treeManager->getBySiteRootId($siteroot->getId());
        $tree->init('element-structure', $element->getEid(), $element->getCreateUserId());
    }
}
