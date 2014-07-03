<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\MessageBundle\Entity\Filter;
use Phlexible\Bundle\MessageBundle\Entity\Repository\FilterRepository;
use Phlexible\Bundle\MessageBundle\Model\FilterManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Doctrine filter manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FilterManager implements FilterManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var FilterRepository
     */
    private $filterRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->filterRepository = $entityManager->getRepository('PhlexibleMessageBundle:Filter');
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return new Filter();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->filterRepository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->filterRepository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, $orderBy = null)
    {
        return $this->filterRepository->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function updateFilter(Filter $filter)
    {
        $this->entityManager->persist($filter);
        $this->entityManager->flush($filter);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFilter(Filter $filter)
    {
        $this->entityManager->remove($filter);
        $this->entityManager->flush();
    }
}
