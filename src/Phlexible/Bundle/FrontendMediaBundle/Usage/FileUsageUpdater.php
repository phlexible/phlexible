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
use Phlexible\Bundle\MediaManagerBundle\Entity\FileUsage;
use Phlexible\Bundle\TeaserBundle\Doctrine\TeaserManager;
use Phlexible\Bundle\TreeBundle\Tree\TreeManager;
use Phlexible\Component\Volume\VolumeManager;

/**
 * File usage updater
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

            $linkVersion = $fileLink->getElementVersion()->getVersion();
            $old = true;

            // add flag STATUS_LATEST if this link is a link to the latest element version
            if ($linkVersion === $element->getLatestVersion()) {
                $flags[$fileId][$fileVersion] |= FileUsage::STATUS_LATEST;
                $old = false;
            }

            // add flag STATUS_ONLINE if this link is used in an online teaser version
            $teasers = $this->teaserManager->findBy(['typeId' => $eid, 'type' => 'element']);
            foreach ($teasers as $teaser) {
                if ($this->teaserManager->getPublishedVersion($teaser, $fileLink->getLanguage()) === $linkVersion) {
                    $flags[$fileId][$fileVersion] |= FileUsage::STATUS_ONLINE;
                    $old = false;
                    break;
                }
            }

            // add flag STATUS_ONLINE if this link is used in an online treeNode version
            $trees = $this->treeManager->getByTypeId($eid, 'element');
            foreach ($trees as $tree) {
                $treeNodes = $tree->getByTypeId($eid, 'element');
                foreach ($treeNodes as $treeNode) {
                    if ($tree->getPublishedVersion($treeNode, $fileLink->getLanguage()) === $linkVersion) {
                        $flags[$fileId][$fileVersion] |= FileUsage::STATUS_ONLINE;
                        $old = false;
                        break;
                    }
                }
            }

            // add flag STATUS_OLD if this link is neither used in latest element version nor online version
            if ($old) {
                $flags[$fileId][$fileVersion] |= FileUsage::STATUS_OLD;
            }
        }

        foreach ($flags as $fileId => $fileVersions) {
            foreach ($fileVersions as $fileVersion => $flag) {

                $volume = $this->volumeManager->getByFileId($fileId);
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
