<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Site;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MediaManagerBundle\Entity\FileUsage;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Delete file checker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DeleteFileChecker
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
     * @param FileInterface $file
     *
     * @return bool
     */
    public function isDeleteAllowed(FileInterface $file)
    {
        if (!$this->securityContext->isGranted('FILE_DELETE', $file)) {
            return false;
        }

        if ($this->deletePolicy === 'delete_all') {
            return true;
        }

        $fileUsageRepository = $this->entityManager->getRepository('PhlexibleMediaManagerBundle:FileUsage');
        $fileUsages = $fileUsageRepository->findBy(array('file' => $file));

        foreach ($fileUsages as $fileUsage) {
            if (in_array($fileUsage->getStatus(), array(FileUsage::STATUS_ONLINE, FileUsage::STATUS_LATEST))) {
                return false;
            }
            if ($fileUsage->getStatus() === FileUsage::STATUS_OLD && $this->deletePolicy === 'hide_old') {
                return false;
            }
        }

        return true;
    }
}
