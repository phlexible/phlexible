<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;

/**
 * Siteroot identifier
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class SiterootManager implements SiterootManagerInterface
{
    /**
     * @var EntityRepository
     */
    private $siterootRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityMAnager = $entityManager;
        $this->siterootRepository = $entityManager->getRepository('PhlexibleSiterootBundle:Siteroot');
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->siterootRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->siterootRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function updateSiteroot(Siteroot $siteroot)
    {
        $this->entityMAnager->flush($siteroot);
    }
}
