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
    const STATUS_ONLINE = 8;
    const STATUS_LATEST = 4;
    const STATUS_OLD = 2;
    const STATUS_DEAD = 1;

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

            // add flag STATUS_LATEST if this link is a link to the latest element version
            if ($folderLink->getElementVersion()->getVersion() === $element->getLatestVersion()) {
                $flags[$folderId] |= self::STATUS_LATEST;
            }

            // add flag STATUS_ONLINE if this link is used in an online teaser version
            $teasers = $this->teaserManager->findBy(array('typeId' => $eid, 'type' => 'element'));
            foreach ($teasers as $teaser) {
                if ($this->teaserManager->getPublishedVersion($teaser, $folderLink->getLanguage()) === $folderLink->getElementVersion()->getVersion()) {
                    $flags[$folderId] |= self::STATUS_ONLINE;
                    break;
                }
            }

            // add flag STATUS_ONLINE if this link is used in an online treeNode version
            $tree = $this->treeManager->getByTypeId($eid, 'element');
            $treeNodes = $tree->getByTypeId($eid, 'element');
            foreach ($treeNodes as $treeNode) {
                if ($tree->getPublishedVersion($treeNode, $folderLink->getLanguage()) === $folderLink->getElementVersion()->getVersion()) {
                    $flags[$folderId] |= self::STATUS_ONLINE;
                    break;
                }
            }

            // add flag STATUS_OLD if this link is neither used in latest element version nor online version
            if (!$flags && self::STATUS_LATEST && !$flags && self::STATUS_ONLINE) {
                $flags[$folderId] |= self::STATUS_OLD;
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
                $folderUsage->setStatus($flag);
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
