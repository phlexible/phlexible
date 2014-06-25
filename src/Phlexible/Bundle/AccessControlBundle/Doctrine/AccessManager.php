<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\AccessControlBundle\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\AccessControlBundle\AccessControlEvents;
use Phlexible\Bundle\AccessControlBundle\Entity\AccessControlEntry;
use Phlexible\Bundle\AccessControlBundle\Event\AccessControlEntryEvent;
use Phlexible\Bundle\AccessControlBundle\Event\BeforeRemoveRightEvent;
use Phlexible\Bundle\AccessControlBundle\Event\BeforeSetRightEvent;
use Phlexible\Bundle\AccessControlBundle\Event\RemoveRightEvent;
use Phlexible\Bundle\AccessControlBundle\Event\SetRightEvent;
use Phlexible\Bundle\AccessControlBundle\Model\AccessManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Access manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class AccessManager implements AccessManagerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var EntityRepository
     */
    private $entityRepository;

    /**
     * @var array
     */
    private $rights;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;

        $this->entityRepository = $entityManager->getRepository('PhlexibleAccessControlBundle:AccessControlEntry');
    }

    /**
     * @param array  $criteria
     *
     * @return array
     * @throws \Exception
     */
    public function findBy(array $criteria)
    {
        $this->loadRights();

        $rights = array();
        foreach ($this->rights as $right) {
            foreach ($criteria as $key => $value) {
                $isArray = is_array($value);
                if ($isArray && !in_array($right[$key], $value)) {
                    continue 2;
                } elseif (!$isArray && $right[$key] != $value) {
                    continue 2;
                }
            }
            $rights[] = $right;
        }

        return $rights;
    }

    /**
     * @param string  $rightType
     * @param string  $contentType
     * @param string  $contentId
     * @param string  $objectType
     * @param string  $objectId
     * @param string  $permission
     * @param integer $inherit
     * @param string  $contentLanguage
     *
     * @return $this
     */
    public function setRight($rightType, $contentType, $contentId, $objectType, $objectId, $permission, $inherit = 1, $contentLanguage = null)
    {
        if ($contentLanguage === '_all_') {
            $contentLanguage = null;
        }

        $ace = new AccessControlEntry();
        $ace
            ->setRightType($rightType)
            ->setContentType($contentType)
            ->setContentId($contentId)
            ->setObjectType($objectType)
            ->setObjectId($objectId)
            ->setContentLanguage($contentLanguage)
            ->setPermission($permission)
            ->setInherit($inherit)
        ;

        $event = new AccessControlEntryEvent($ace);
        if ($this->dispatcher->dispatch(AccessControlEvents::BEFORE_SET_RIGHT, $event)->isPropagationStopped()) {
            return $this;
        }

        $this->entityManager->persist($ace);
        $this->entityManager->flush($ace);

        $event = new AccessControlEntryEvent($ace);
        $this->dispatcher->dispatch(AccessControlEvents::SET_RIGHT, $event);

        return $this;
    }

    /**
     * @param string $rightType
     * @param string $contentType
     * @param string $contentId
     * @param string $objectType
     * @param string $objectId
     * @param string $permission
     * @param string $language
     *
     * @return $this
     */
    public function removeRight($rightType, $contentType, $contentId, $objectType = null, $objectId = null, $permission = null, $language = null)
    {
        $criteria = array(
            'right_type'       => $rightType,
            'content_type'     => $contentType,
            'content_id'       => $contentId,
            'content_language' => null,
        );

        if ($objectType !== null) {
            $criteria['object_type'] = $objectType;
        }

        if ($objectId !== null) {
            $criteria['object_id'] = $objectId;
        }

        if ($permission !== null) {
            $criteria['permission'] = $permission;
        }

        if ($language !== null && $language !== '_all_') {
            $criteria['content_language'] = $language;
        }

        $aces = $this->findBy($criteria);

        foreach ($aces as $ace) {
            $event = new AccessControlEntryEvent($ace);
            if (!$this->dispatcher->dispatch(AccessControlEvents::BEFORE_REMOVE_RIGHT, $event)) {
                return $this;
            }

            $this->entityManager->remove($ace);

            $event = new AccessControlEntryEvent($ace);
            $this->dispatcher->dispatch(AccessControlEvents::REMOVE_RIGHT, $event);
        }

        $this->entityManager->flush();

        return $this;
    }

    private function loadRights()
    {
        if ($this->rights !== null) {
            return;
        }

        $qb = $this->entityRepository->createQueryBuilder('a');
        $this->rights = $qb->getQuery()->getResult();
    }
}
