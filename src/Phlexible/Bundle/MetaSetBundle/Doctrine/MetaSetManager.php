<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSet;
use Phlexible\Bundle\MetaSetBundle\Entity\MetaSetField;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSetManagerInterface;

/**
 * Doctrine meta set manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetManager implements MetaSetManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository
     */
    private $metaSetRepository;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->metaSetRepository = $entityManager->getRepository('PhlexibleMetaSetBundle:MetaSet');
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return $this->metaSetRepository->find($id);
    }

    /**
     * @param string $name
     *
     * @return MetaSet
     */
    public function findOneByName($name)
    {
        return $this->metaSetRepository->findOneBy(array('name' => $name));
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->metaSetRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaSet()
    {
        return new MetaSet();
    }

    /**
     * {@inheritdoc}
     */
    public function createMetaSetField()
    {
        return new MetaSetField();
    }

    /**
     * {@inheritdoc}
     */
    public function updateMetaSet(MetaSet $metaSet)
    {
        $this->entityManager->persist($metaSet);
        $this->entityManager->flush($metaSet);
    }
}