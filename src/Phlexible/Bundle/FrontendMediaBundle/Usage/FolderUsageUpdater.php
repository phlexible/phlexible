<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Usage;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\MediaManagerBundle\Entity\FolderUsage;
use Phlexible\Bundle\TeaserBundle\Doctrine\TeaserManager;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Phlexible\Component\Volume\VolumeManager;

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
     * @var VolumeManager
     */
    private $volumeManager;

    /**
     * @param EntityManager  $entityManager
     * @param TreeManager    $treeManager
     * @param TeaserManager  $teaserManager
     * @param VolumeManager  $volumeManager
     */
    public function __construct(
        EntityManager $entityManager,
        TreeManager $treeManager,
        TeaserManager $teaserManager,
        VolumeManager $volumeManager)
    {
        $this->entityManager = $entityManager;
        $this->treeManager = $treeManager;
        $this->teaserManager = $teaserManager;
        $this->volumeManager = $volumeManager;
    }

    public function removeObsolete()
    {
        $fileUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FolderUsage');

        $qb = $fileUsageRepository->createQueryBuilder('fu');
        $qb
            ->delete()
            ->where($qb->expr()->eq('fu.usageType', $qb->expr()->literal('element')))
            ->andWhere($qb->expr()->notIn('fu.usageId', 'SELECT e.eid FROM Phlexible\\Bundle\\ElementBundle\\Entity\\Element e'));

        $qb->getQuery()->execute();
    }

    /**
     * @param int $eid
     */
    public function removeUsage($eid)
    {
        $fileUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FolderUsage');

        $qb = $fileUsageRepository->createQueryBuilder('fu');
        $qb
            ->delete()
            ->where($qb->expr()->eq('fu.usageType', $qb->expr()->literal('element')))
            ->andWhere($qb->expr()->eq('fu.usageId', $qb->expr()->literal($eid)));

        $qb->getQuery()->execute();
    }

    /**
     * @param Element $element
     * @param bool    $flush
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
            ->where($qb->expr()->eq('e.eid', $eid))
            ->andWhere($qb->expr()->eq('l.type', $qb->expr()->literal('folder')));
        $folderLinks = $qb->getQuery()->getResult();
        /* @var $folderLinks ElementLink[] */

        $flags = [];

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
            $teasers = $this->teaserManager->findBy(['typeId' => $eid, 'type' => 'element']);
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
            $volume = $this->volumeManager->findByFolderId($folderId);
            if (!$volume) {
                continue;
            }
            $folder = $volume->findFolder($folderId);

            $folderUsage = $folderUsageRepository->findOneBy(['folder' => $folder, 'usageType' => 'element', 'usageId' => $eid]);
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
