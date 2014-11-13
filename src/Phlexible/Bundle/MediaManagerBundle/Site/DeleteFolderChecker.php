<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Site;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MediaManagerBundle\Entity\FolderUsage;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Delete folder checker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteFolderChecker
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var string
     */
    private $deletePolicy;

    /**
     * @param EntityManager            $entityManager
     * @param SecurityContextInterface $securityContext
     * @param string                   $deletePolicy
     */
    public function __construct(EntityManager $entityManager, SecurityContextInterface $securityContext, $deletePolicy)
    {
        $this->entityManager = $entityManager;
        $this->securityContext = $securityContext;
        $this->deletePolicy = $deletePolicy;
    }

    /**
     * @param FolderInterface $folder
     *
     * @return bool
     */
    public function isDeleteAllowed(FolderInterface $folder)
    {
        if (!$this->securityContext->isGranted('FOLDER_DELETE', $folder)) {
            return false;
        }

        if ($this->deletePolicy === 'delete_all') {
            return true;
        }

        $folderUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FolderUsage');
        $folderUsages = $folderUsageRepository->findBy(['folder' => $folder]);

        foreach ($folderUsages as $folderUsage) {
            if (in_array($folderUsage->getStatus(), [FolderUsage::STATUS_ONLINE, FolderUsage::STATUS_LATEST])) {
                return false;
            }
            if ($folderUsage->getStatus() === FolderUsage::STATUS_OLD && $this->deletePolicy === 'hide_old') {
                return false;
            }
        }

        return true;
    }
}
