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
use Phlexible\Bundle\MediaManagerBundle\Entity\FileUsage;
use Phlexible\Bundle\TeaserBundle\Doctrine\TeaserManager;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Phlexible\Component\Volume\VolumeManager;

/**
 * File usage updater.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FileUsageUpdater
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
     * @param EntityManager $entityManager
     * @param TreeManager   $treeManager
     * @param TeaserManager $teaserManager
     * @param VolumeManager $volumeManager
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
        $fileUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FileUsage');

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
        $fileUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FileUsage');

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
        $fileUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FileUsage');

        $qb = $elementLinkRepository->createQueryBuilder('l');
        $qb
            ->select('l')
            ->join('l.elementVersion', 'ev')
            ->join('ev.element', 'e')
            ->where($qb->expr()->eq('e.eid', $eid))
            ->andWhere($qb->expr()->eq('l.type', $qb->expr()->literal('file')));
        $fileLinks = $qb->getQuery()->getResult();
        /* @var $fileLinks ElementLink[] */

        $teasers = $this->teaserManager->findBy(['typeId' => $eid]);
        $trees = $this->treeManager->getByTypeId($eid);
        $treeNodes = array();
        foreach ($trees as $tree) {
            foreach ($tree->getByTypeId($eid) as $treeNode) {
                $treeNodes[] = $treeNode;
            }
        }
        $teaserOnlineVersions = array();
        $nodeOnlineVersions = array();
        $flags = [];

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
        }

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

            $linkVersion = $fileLink->getElementVersion()->getVersion();
            $old = true;

            // add flag STATUS_LATEST if this link is a link to the latest element version
            if ($linkVersion === $element->getLatestVersion()) {
                $flags[$fileId][$fileVersion] |= FileUsage::STATUS_LATEST;
                $old = false;
            }

            // add flag STATUS_ONLINE if this link is used in an online teaser version
            foreach ($teasers as $teaser) {
                $cacheId = $teaser->getId().'_'.$fileLink->getLanguage();
                if (!isset($teaserOnlineVersions[$cacheId])) {
                    $teaserOnlineVersions[$cacheId] = $this->teaserManager->getPublishedVersion($teaser, $fileLink->getLanguage());
                }

                if ($teaserOnlineVersions[$cacheId] === $linkVersion) {
                    $flags[$fileId][$fileVersion] |= FileUsage::STATUS_ONLINE;
                    $old = false;
                    break;
                }
            }

            // add flag STATUS_ONLINE if this link is used in an online treeNode version
            foreach ($treeNodes as $treeNode) {
                $cacheId = $treeNode->getId().'_'.$fileLink->getLanguage();
                if (!isset($nodeOnlineVersions[$cacheId])) {
                    $nodeOnlineVersions[$cacheId] = $treeNode->getTree()->getPublishedVersion($treeNode, $fileLink->getLanguage());
                }

                if ($nodeOnlineVersions[$cacheId] === $linkVersion) {
                    $flags[$fileId][$fileVersion] |= FileUsage::STATUS_ONLINE;
                    $old = false;
                    break;
                }
            }

            // add flag STATUS_OLD if this link is neither used in latest element version nor online version
            if ($old) {
                $flags[$fileId][$fileVersion] |= FileUsage::STATUS_OLD;
            }
        }

        foreach ($flags as $fileId => $fileVersions) {
            foreach ($fileVersions as $fileVersion => $flag) {
                $volume = $this->volumeManager->findByFileId($fileId);
                if (!$volume) {
                    continue;
                }
                $file = $volume->findFile($fileId, $fileVersion);

                $qb = $fileUsageRepository->createQueryBuilder('fu');
                $qb
                    ->select('fu')
                    ->join('fu.file', 'f')
                    ->where($qb->expr()->eq('fu.usageType', $qb->expr()->literal('element')))
                    ->andWhere($qb->expr()->eq('fu.usageId', $eid))
                    ->andWhere($qb->expr()->eq('f.id', $qb->expr()->literal($file->getId())))
                    ->andWhere($qb->expr()->eq('f.version', $file->getVersion()))
                    ->setMaxResults(1);
                $fileUsages = $qb->getQuery()->getResult();
                if (!count($fileUsages)) {
                    if (!$flag) {
                        continue;
                    }
                    $folderUsage = new FileUsage($file, 'element', $eid, $flag);
                    $this->entityManager->persist($folderUsage);
                } else {
                    $fileUsage = current($fileUsages);

                    if ($flag) {
                        $fileUsage->setStatus($flag);
                    } else {
                        $this->entityManager->remove($fileUsage);
                    }
                }
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
