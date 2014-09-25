<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Usage;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\MediaManagerBundle\Entity\FolderUsage;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Bundle\TeaserBundle\Doctrine\TeaserManager;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;

/**
 * File usage
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderUsageUpdater
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TreeManager
     */
    private $treeManager;

    /**
     * @var TeaserManager
     */
    private $teaserManager;

    /**
     * @var SiteManager
     */
    private $siteManager;

    /**
     * @param EntityManager  $entityManager
     * @param TreeManager    $treeManager
     * @param TeaserManager  $teaserManager
     * @param SiteManager    $siteManager
     */
    public function __construct(
        EntityManager $entityManager,
        TreeManager $treeManager,
        TeaserManager $teaserManager,
        SiteManager $siteManager)
    {
        $this->entityManager = $entityManager;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->siteManager = $siteManager;
    }

    /**
     * @param Element $element
     * @param bool    $flush
     *
     * @return array
     */
    public function updateUsage(Element $element, $flush = true)
    {
        $eid = $element->getEid();

        $elementLinkRepository = $this->entityManager->getRepository('PhlexibleElementBundle:ElementLink');
        $folderUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FolderUsage');

        $qb = $elementLinkRepository->createQueryBuilder('l');
        $qb
            ->select('l')
            ->join('l.elementVersion', 'ev')
            ->join('ev.element', 'e')
            ->where('e.eid = 9')
            ->andWhere($qb->expr()->eq('l.type', $qb->expr()->literal('folder')));
        $folderLinks = $qb->getQuery()->getResult();
        /* @var $folderLinks ElementLink[] */

        $flags = array();

        foreach ($folderLinks as $folderLink) {
            $folderId = $folderLink->getTarget();

            if (!isset($flags[$folderId])) {
                $flags[$folderId] = 0;
            }

            $linkVersion = $folderLink->getElementVersion()->getVersion();
            $old = true;

            // add flag STATUS_LATEST if this link is a link to the latest element version
            if ($linkVersion === $element->getLatestVersion()) {
                $flags[$folderId] |= FolderUsage::STATUS_LATEST;
                $old = false;
            }

            // add flag STATUS_ONLINE if this link is used in an online teaser version
            $teasers = $this->teaserManager->findBy(array('typeId' => $eid, 'type' => 'element'));
            foreach ($teasers as $teaser) {
                if ($this->teaserManager->getPublishedVersion($teaser, $folderLink->getLanguage()) === $linkVersion) {
                    $flags[$folderId] |= FolderUsage::STATUS_ONLINE;
                    $old = false;
                    break;
                }
            }

            // add flag STATUS_ONLINE if this link is used in an online treeNode version
            $trees = $this->treeManager->getByTypeId($eid, 'element');
            foreach ($trees as $tree) {
                $treeNodes = $tree->getByTypeId($eid, 'element');
                foreach ($treeNodes as $treeNode) {
                    if ($tree->getPublishedVersion($treeNode, $folderLink->getLanguage()) === $linkVersion) {
                        $flags[$folderId] |= FolderUsage::STATUS_ONLINE;
                        $old = false;
                        break;
                    }
                }
            }

            // add flag STATUS_OLD if this link is neither used in latest element version nor online version
            if ($old) {
                $flags[$folderId] |= FolderUsage::STATUS_OLD;
            }
        }

        foreach ($flags as $folderId => $flag) {
            $site = $this->siteManager->getByFolderId($folderId);
            $folder = $site->findFolder($folderId);

            $folderUsage = $folderUsageRepository->findOneBy(array('folder' => $folder, 'usageType' => 'element', 'usageId' => $eid));
            if (!$folderUsage) {
                $folderUsage = new FolderUsage($folder, 'element', $eid, $flag);
                $this->entityManager->persist($folderUsage);
            } else {
                if ($flag) {
                    $folderUsage->setStatus($flag);
                } else {
                    $this->entityManager->remove($folderUsage);
                }
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
