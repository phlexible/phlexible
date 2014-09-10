<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle\ElementFinder;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\ElementBundle\Meta\ElementMetaDataManager;
use Phlexible\Bundle\ElementBundle\Meta\ElementMetaSetResolver;
use Phlexible\Bundle\ElementFinderBundle\ElementFinderEvents;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderLookupElement;
use Phlexible\Bundle\ElementFinderBundle\Entity\ElementFinderLookupMeta;
use Phlexible\Bundle\ElementFinderBundle\Event\UpdateLookupElement;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Lookup builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LookupBuilder
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var ElementMetaSetResolver
     */
    private $metasetResolver;

    /**
     * @var ElementMetaDataManager
     */
    private $metadataManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EntityManager            $entityManager
     * @param ElementService           $elementService
     * @param ElementMetaSetResolver   $metasetResolver
     * @param ElementMetaDataManager   $metadataManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EntityManager $entityManager,
        ElementService $elementService,
        ElementMetaSetResolver $metasetResolver,
        ElementMetaDataManager $metadataManager,
        EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->elementService = $elementService;
        $this->metasetResolver = $metasetResolver;
        $this->metadataManager = $metadataManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getLookupElementRepository()
    {
        return $this->entityManager->getRepository('PhlexibleElementFinderBundle:ElementFinderLookupElement');
    }

    /**
     * @return EntityRepository
     */
    private function getLookupMetaRepository()
    {
        return $this->entityManager->getRepository('PhlexibleElementFinderBundle:ElementFinderLookupMeta');
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param bool              $flush
     */
    public function remove(TreeNodeInterface $treeNode, $flush = true)
    {
        foreach ($this->getLookupElementRepository()->findBy(array('treeId' => $treeNode->getId())) as $lookupElement) {
            $this->entityManager->remove($lookupElement);
        }

        foreach ($this->getLookupMetaRepository()->findBy(array('treeId' => $treeNode->getId())) as $lookupMeta) {
            $this->entityManager->remove($lookupMeta);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param bool              $flush
     */
    public function removePreview(TreeNodeInterface $treeNode, $flush = true)
    {
        foreach ($this->getLookupElementRepository()->findBy(array('treeId' => $treeNode->getId(), 'isPreview' => true)) as $lookupElement) {
            $this->entityManager->remove($lookupElement);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param bool              $flush
     */
    public function removeOnline(TreeNodeInterface $treeNode, $flush = true)
    {
        foreach ($this->getLookupElementRepository()->findBy(array('treeId' => $treeNode->getId(), 'isPreview' => false)) as $lookupElement) {
            $this->entityManager->remove($lookupElement);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param string            $language
     * @param bool              $flush
     */
    public function removeOnlineByTreeNodeAndLanguage(TreeNodeInterface $treeNode, $language, $flush = true)
    {
        foreach ($this->getLookupElementRepository()->findBy(array('treeId' => $treeNode->getId(), 'language' => $language, 'isPreview' => false)) as $lookupElement) {
            $this->entityManager->remove($lookupElement);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param int               $version
     * @param string            $language
     * @param bool              $flush
     */
    public function removeMetaByTreeNodeAndVersionAndLanguage(TreeNodeInterface $treeNode, $version, $language, $flush = true)
    {
        foreach ($this->getLookupMetaRepository()->findBy(array('treeId' => $treeNode->getId(), 'version' => $version, 'language' => $language)) as $lookupMeta) {
            $this->entityManager->remove($lookupMeta);
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param TreeNodeInterface $treeNode
     */
    public function update(TreeNodeInterface $treeNode)
    {
        //$this->updateOnline($treeNode);
        $this->updatePreview($treeNode);
    }

    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return int|null
     */
    public function updatePreview(TreeNodeInterface $treeNode)
    {
        // TODO: repair
        $event = new UpdateLookupElement($treeNode, true);
        if ($this->dispatcher->dispatch(ElementFinderEvents::BEFORE_UPDATE_LOOKUP_ELEMENT, $event)->isPropagationStopped()) {
            return null;
        }

        $element = $this->elementService->findElement($treeNode->getTypeId());
        $elementVersion = $this->elementService->findLatestElementVersion($element);

        $languages = array('de');
        foreach ($languages as $language) {
            $this->updateVersion(
                $treeNode,
                $element,
                $elementVersion,
                true,
                $language,
                null
            );
        }

        $this->entityManager->flush();

        $event = new UpdateLookupElement($treeNode, true);
        $this->dispatcher->dispatch(ElementFinderEvents::UPDATE_LOOKUP_ELEMENT, $event);
    }

    /**
     * @param TreeNodeInterface $treeNode
     *
     * @return int|null
     */
    public function updateOnline(TreeNodeInterface $treeNode)
    {
        $event = new UpdateLookupElement($treeNode, false);
        if ($this->dispatcher->dispatch(ElementFinderEvents::BEFORE_UPDATE_LOOKUP_ELEMENT, $event)->isPropagationStopped()) {
            return null;
        }

        $element = $this->elementService->findElement($treeNode->getTypeId());
        foreach ($treeNode->getTree()->getPublishedVersions($treeNode) as $language => $onlineVersion) {
            $elementVersion = $this->elementService->findLatestElementVersion($element, $onlineVersion);

            $this->updateVersion(
                $treeNode,
                $element,
                $elementVersion,
                false,
                $language,
                $onlineVersion
            );
        }

        $event = new UpdateLookupElement($treeNode, false);
        $this->dispatcher->dispatch(ElementFinderEvents::UPDATE_LOOKUP_ELEMENT, $event);
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param Element           $element
     * @param ElementVersion    $elementVersion
     * @param bool              $preview
     * @param string            $language
     * @param int               $onlineVersion
     */
    private function updateVersion(
        TreeNodeInterface $treeNode,
        Element $element,
        ElementVersion $elementVersion,
        $preview,
        $language,
        $onlineVersion)
    {
        $this->updateMeta($treeNode, $element, $elementVersion, $language);

        $lookupElement = $this->getLookupElementRepository()
            ->findOneBy(
                array(
                    'treeId' => $treeNode->getId(),
                    'isPreview' => $preview,
                    'language' => $language
                )
            );

        if (!$lookupElement) {
            $lookupElement = new ElementFinderLookupElement();
        }

        $lookupElement
            ->setEid($element->getEid())
            ->setTreeId($treeNode->getId())
            ->setPublishedAt($elementVersion->getCreatedAt())
            ->setCustomDate($elementVersion->getCustomDate($language))
            ->setIsPreview($preview)
            ->setElementtypeId($element->getElementtypeId())
            ->setVersion($elementVersion->getVersion())
            ->setLanguage($language)
            ->setOnlineVersion($onlineVersion)
            ->setInNavigation($treeNode->getInNavigation())
            ->setIsRestricted($treeNode->getNeedAuthentication())
            ->setCachedAt(new \DateTime());

        $this->entityManager->persist($lookupElement);
    }

    /**
     * @param TreeNodeInterface $treeNode
     * @param Element           $element
     * @param ElementVersion    $elementVersion
     * @param string            $language
     */
    private function updateMeta(TreeNodeInterface $treeNode, Element $element, ElementVersion $elementVersion, $language)
    {
        $this->removeMetaByTreeNodeAndVersionAndLanguage($treeNode, $elementVersion->getVersion(), $language, false);

        $metaset = $this->metasetResolver->resolve($elementVersion);

        if (!$metaset) {
            return;
        }

        $metadata = $this->metadataManager->findByMetaSetAndElementVersion($metaset, $elementVersion);

        foreach ($metadata->getValues() as $name => $value) {
            $cleanString = str_replace(
                array(',', ';'),
                array('===', '==='),
                html_entity_decode($value, ENT_COMPAT, 'UTF-8')
            );

            $splitValues = explode('===', $cleanString);

            foreach ($splitValues as $splitValue) {
                $lookupMeta = new ElementFinderLookupMeta();
                $lookupMeta
                    ->setTreeId($treeNode->getId())
                    ->setEid($element->getEid())
                    ->setVersion($elementVersion->getVersion())
                    ->setLanguage($language)
                    ->setSetId($metaset->getId())
                    ->setField($name)
                    ->setValue($splitValue);

                $this->entityManager->persist($lookupMeta);
            }
        }
    }
}
