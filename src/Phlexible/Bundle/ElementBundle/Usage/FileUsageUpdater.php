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
use Phlexible\Bundle\MediaManagerBundle\Entity\FileUsage;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteManager;
use Phlexible\Bundle\TeaserBundle\Doctrine\TeaserManager;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;

/**
 * File usage updater
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileUsageUpdater
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
        $fileUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FileUsage');

        $qb = $elementLinkRepository->createQueryBuilder('l');
        $qb
            ->select('l')
            ->join('l.elementVersion', 'ev')
            ->join('ev.element', 'e')
            ->where('e.eid = 9')
            ->andWhere($qb->expr()->eq('l.type', $qb->expr()->literal('file')));
        $fileLinks = $qb->getQuery()->getResult();
        /* @var $fileLinks ElementLink[] */

        $element = $this->elementService->findElement($eid);
        $elementtype = $this->elementService->findElementtype($element);

        $flags = array();

        foreach ($fileLinks as $fileLink) {
            $fileParts = explode(';', $fileLink->getTarget());
            $fileId = $fileParts[0];
            $fileVersion = 1;
            if (isset($fileParts[1])) {
                $fileVersion = $fileParts[1];
            }
            if (!isset($flags[$fileId][$fileVersion])) {
                $flags[$fileId][$fileVersion] = 0;
            }

            if ($fileLink->getElementVersion()->getVersion() === $element->getLatestVersion()) {
                $flags[$fileId][$fileVersion] |= self::STATUS_LATEST;
            }

            if ($elementtype->getType() === 'part') {
                $teasers = $this->teaserManager->findBy(array('typeId' => $eid, 'type' => 'element'));

                foreach ($teasers as $teaser) {
                    if ($this->teaserManager->getPublishedVersion($teaser, $fileLink->getLanguage()) === $fileLink->getElementVersion()->getVersion()) {
                        $flags[$fileId][$fileVersion] |= self::STATUS_ONLINE;
                        break;
                    }
                }
            } elseif ($elementtype->getType() !== 'layout') {
                $tree = $this->treeManager->getByTypeId($eid, 'element');
                $treeNodes = $tree->getByTypeId($eid, 'element');

                foreach ($treeNodes as $treeNode) {
                    if ($tree->getPublishedVersion($treeNode, $fileLink->getLanguage()) === $fileLink->getElementVersion()->getVersion()) {
                        $flags[$fileId][$fileVersion] |= self::STATUS_ONLINE;
                        break;
                    }
                }
            }

            $flags[$fileId][$fileVersion] |= self::STATUS_OLD;
        }

        foreach ($flags as $fileId => $fileVersions) {
            foreach ($fileVersions as $fileVersion => $flag) {
                $site = $this->siteManager->getByFileId($fileId);
                $file = $site->findFile($fileId, $fileVersion);

                $qb = $fileUsageRepository->createQueryBuilder('fu');
                $qb
                    ->select('fu')
                    ->join('fu.file', 'f')
                    ->where($qb->expr()->eq('fu.usageType', $qb->expr()->literal('element')))
                    ->andWhere($qb->expr()->eq('fu.usageId', $eid))
                    ->andWhere($qb->expr()->eq('f.id', $qb->expr()->literal($file->getId())))
                    ->andWhere($qb->expr()->eq('f.version', $file->getVersion()));
                $fileUsage = $qb->getQuery()->getSingleResult();
                if (!$fileUsage) {
                    $folderUsage = new FileUsage($file, 'element', $eid, $flag);
                    $this->entityManager->persist($folderUsage);
                } else {
                    $fileUsage->setStatus($flag);
                }
            }
        }

        $this->entityManager->flush();
    }
}
