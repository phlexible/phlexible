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
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersionMappedField;
use Phlexible\Bundle\ElementtypeBundle\Entity\ElementtypeStructureNode;
use Phlexible\Bundle\ElementtypeBundle\Model\ElementtypeStructure;
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
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var FieldMapper
     */
    private $fieldMapper;

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
     * @param TreeManager              $treeManager
     * @param SecurityContextInterface $securityContext
     * @param string                   $masterLanguage
     */
    public function __construct(ElementService $elementService, TreeManager $treeManager, SecurityContextInterface $securityContext, $masterLanguage)
    {
        $this->elementService = $elementService;
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

        $elementtypeVersion = $this->elementService->getElementtypeService()->createElementtype(
            'structure',
            'site_root_' . $siteroot->getId(),
            'Site root ' . $siteroot->getTitle(),
            'www_root.gif',
            $siteroot->getModifyUserId(),
            false
        );

        $elementtypeStructure = new ElementtypeStructure();

        $root = new ElementtypeStructureNode();
        $root
            ->setVersion($elementtypeVersion->getVersion())
            ->setElementtype($elementtypeVersion->getElementtype())
            ->setElementtypeStructure($elementtypeStructure)
            ->setDsId(Uuid::generate())
            ->setName('root')
            ->setType('root')
            ->setSort(1);

        $tab = new ElementtypeStructureNode();
        $tab
            ->setVersion($elementtypeVersion->getVersion())
            ->setElementtype($elementtypeVersion->getElementtype())
            ->setElementtypeStructure($elementtypeStructure)
            ->setParentNode($root)
            ->setParentDsId($root->getDsId())
            ->setDsId(Uuid::generate())
            ->setName('data')
            ->setType('tab')
            ->setSort(2)
            ->setLabels(array('fieldlabel' => array('de' => 'Daten', 'en' => 'Data')))
            ->setConfiguration(array())
            ->setContentChannels(array())
            ->setValidation(array())
            ->setOptions(array());

        $textfield = new ElementtypeStructureNode();
        $textfield
            ->setVersion($elementtypeVersion->getVersion())
            ->setElementtype($elementtypeVersion->getElementtype())
            ->setElementtypeStructure($elementtypeStructure)
            ->setParentNode($tab)
            ->setParentDsId($tab->getDsId())
            ->setDsId(Uuid::generate())
            ->setName('title')
            ->setType('textfield')
            ->setSort(3)
            ->setLabels(array('fieldlabel' => array('de' => 'Titel', 'en' => 'Title')))
            ->setConfiguration(array())
            ->setContentChannels(array())
            ->setValidation(array('required' => 'always'))
            ->setOptions(array());

        $elementtypeStructure
            ->setElementtypeVersion($elementtypeVersion)
            ->addNode($root)
            ->addNode($tab)
            ->addNode($textfield);

        $this->elementService->getElementtypeService()->updateElementtypeStructure($elementtypeStructure, false);

        $elementtypeVersion->setMappings(array('backend' => array('fields' => array(array('ds_id' => $textfield->getDsId(), 'field' => 'Title', 'index' => 1)), 'pattern' => '$1')));

        $element = new Element();
        $element
            ->setElementtype($elementtypeVersion->getElementtype())
            ->setLatestVersion(1)
            ->setMasterLanguage($this->masterLanguage)
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($siteroot->getModifyUserId())
            ->setUniqueId('site_root_' . $siteroot->getId());

        $elementVersion = new ElementVersion();
        $elementVersion
            ->setElement($element)
            ->setElementtypeVersion($elementtypeVersion->getVersion())
            ->setVersion(1)
            ->setTriggerLanguage($this->masterLanguage)
            ->setComment('Created by siteroot creation.')
            ->setCreatedAt(new \DateTime())
            ->setCreateUserId($siteroot->getModifyUserId());

        $this->elementService->updateElement($element, false);
        $this->elementService->updateElementVersion($elementVersion);

        $tree = $this->treeManager->getBySiteRootId($siteroot->getId());
        $tree->init('element', $element->getEid(), $element->getCreateUserId());
    }
}
