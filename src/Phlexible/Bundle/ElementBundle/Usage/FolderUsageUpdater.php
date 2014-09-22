<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Usage;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\ElementService;
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
     * @var ElementService
     */
    private $elementService;

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
     * @param ElementService $elementService
     * @param TreeManager    $treeManager
     * @param TeaserManager  $teaserManager
     * @param SiteManager    $siteManager
     */
    public function __construct(
        EntityManager $entityManager,
        ElementService $elementService,
        TreeManager $treeManager,
        TeaserManager $teaserManager,
        SiteManager $siteManager)
    {
        $this->entityManager = $entityManager;
        $this->elementService = $elementService;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->siteManager = $siteManager;
    }

    /**
     * @param $eid
     *
     * @return array
     */
    public function updateUsage($eid)
    {
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

        $element = $this->elementService->findElement($eid);
        $elementtype = $this->elementService->findElementtype($element);

        $flags = array();

        foreach ($folderLinks as $folderLink) {
            $folderId = $folderLink->getTarget();
            if (!isset($flags[$folderId])) {
                $flags[$folderId] = 0;
            }

            if ($folderLink->getElementVersion()->getVersion() === $element->getLatestVersion()) {
                $flags[$folderId] |= self::STATUS_LATEST;
            }

            if ($elementtype->getType() === 'part') {
                $teasers = $this->teaserManager->findBy(array('typeId' => $eid, 'type' => 'element'));

                foreach ($teasers as $teaser) {
                    if ($this->teaserManager->getPublishedVersion($teaser, $folderLink->getLanguage()) === $folderLink->getElementVersion()->getVersion()) {
                        $flags[$folderId] |= self::STATUS_ONLINE;
                        break;
                    }
                }
            } elseif ($elementtype->getType() !== 'layout') {
                $tree = $this->treeManager->getByTypeId($eid, 'element');
                $treeNodes = $tree->getByTypeId($eid, 'element');

                foreach ($treeNodes as $treeNode) {
                    if ($tree->getPublishedVersion($treeNode, $folderLink->getLanguage()) === $folderLink->getElementVersion()->getVersion()) {
                        $flags[$folderId] |= self::STATUS_ONLINE;
                        break;
                    }
                }
            }

            $flags[$folderId] |= self::STATUS_OLD;
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

        $this->entityManager->flush();
    }
}
