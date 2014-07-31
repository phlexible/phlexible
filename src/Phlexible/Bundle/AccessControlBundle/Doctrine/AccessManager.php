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
    private $accessControlEntryRepository;

    /**
     * @var AccessControlEntry[]
     */
    private $entries;

    /**
     * @param EntityManager            $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EntityManager $entityManager, EventDispatcherInterface $dispatcher)
    {
        $this->entityManager = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return EntityRepository
     */
    private function getAccessControlEntryRepository()
    {
        if (null === $this->accessControlEntryRepository) {
            $this->accessControlEntryRepository = $this->entityManager->getRepository('PhlexibleAccessControlBundle:AccessControlEntry');
        }

        return $this->accessControlEntryRepository;
    }

    /**
     * @param array  $criteria
     *
     * @return array
     * @throws \Exception
     */
    public function findBy(array $criteria)
    {
        $this->loadEntries();

        $rights = array();
        foreach ($this->entries as $entry) {
            foreach ($criteria as $key => $value) {
                $isArray = is_array($value);
                if ($isArray && !in_array($entry[$key], $value)) {
                    continue 2;
                } elseif (!$isArray && $entry[$key] != $value) {
                    continue 2;
                }
            }
            $rights[] = $entry;
        }

        return $rights;
    }

    /**
     * {@inheritdoc}
     */
    public function findByContentIdPath($type, $contentType, array $contentIdPath, array $securityTypes, $contentLanguage = null)
    {
        $this->loadEntries();

        $entries = array();
        foreach ($this->entries as $entry) {
            foreach ($securityTypes as $securityType) {
                foreach ($contentIdPath as $contentId) {
                    if ($type === $entry->getType()
                            && $contentType === $entry->getContentType()
                            && $contentId === $entry->getContentId()
                            && $securityType === $entry->getSecurityType()
                            && $contentLanguage === $entry->getContentLanguage()) {
                        $entries[] = $entry;
                    }
                }
            }
        }

        return $entries;
    }

    /**
     * {@inheritdoc}
     */
    public function findOneByValues($type, $contentType, $contentId, $securityType, $securityId, $contentLanguage = null)
    {
        $this->loadEntries();

        foreach ($this->entries as $entry) {
            if ($type === $entry->getType()
                    && $contentType === $entry->getContentType()
                    && $contentId === $entry->getContentId()
                    && $securityType === $entry->getSecurityType()
                    && $securityId === $entry->getSecurityId()
                    && $contentLanguage === $entry->getContentLanguage()) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * @param string $type
     * @param string $contentType
     * @param string $contentId
     * @param string $securityType
     * @param string $securityId
     * @param string $permission
     * @param int    $inherit
     * @param string $contentLanguage
     *
     * @return $this
     */
    public function setRight($type, $contentType, $contentId, $securityType, $securityId, $permission, $inherit = 1, $contentLanguage = null)
    {
        if ($contentLanguage === '_all_') {
            $contentLanguage = null;
        }

        $ace = $this->findOneByValues($type, $contentType, $contentId, $securityType, $securityId);
        if (!$ace) {
            $ace = new AccessControlEntry();
            $ace
                ->setType($type)
                ->setContentType($contentType)
                ->setContentId($contentId)
                ->setSecurityType($securityType)
                ->setSecurityId($securityId)
                ->setContentLanguage($contentLanguage);
        }

        $mask = 0;
        $stopMask = 0;
        $inheritMask = 0;

        $ace
            ->setMask($mask)
            ->setStopMask($stopMask)
            ->setNoInheritMask($inheritMask);

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
     * @param string $type
     * @param string $contentType
     * @param string $contentId
     * @param string $securityType
     * @param string $securityId
     * @param string $permission
     * @param string $language
     *
     * @return $this
     */
    public function removeRight($type, $contentType, $contentId, $securityType = null, $securityId = null, $permission = null, $language = null)
    {
        $criteria = array(
            'right_type'       => $type,
            'content_type'     => $contentType,
            'content_id'       => $contentId,
            'content_language' => null,
        );

        if ($securityType !== null) {
            $criteria['object_type'] = $securityType;
        }

        if ($securityId !== null) {
            $criteria['object_id'] = $securityId;
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

    private function loadEntries()
    {
        if ($this->entries !== null) {
            return;
        }

        $qb = $this->getAccessControlEntryRepository()->createQueryBuilder('a');
        $this->entries = $qb->getQuery()->getResult();
    }
}
