<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\LockBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\LockBundle\Entity\Lock;

/**
 * Lock repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LockRepository extends EntityRepository
{
    /**
     * Retrieve lock by identifier
     *
     * @param string $identifier
     * @param string $userId
     *
     * @return Lock
     */
    public function findOneByIdentifierAndUserId($identifier, $userId)
    {
        $row = $this->datamapper->getBy(array('id' => $identifier, 'user_id' => $userId));

        if (!$row) {
            return null;
        }

        $lock = $this->mapRow($row);

        return $lock;
    }

    /**
     * Retrieve lock by identifier
     *
     * @param string $identifier
     * @param string $notUserId
     *
     * @return Lock
     */
    public function findOneByIdentifierAndNotUserId($identifier, $notUserId)
    {
        $row = $this->datamapper->getOneByIdentifierAndNotUserId($identifier, $notUserId);

        $lock = $this->mapRow($row);

        return $lock;
    }

    /**
     * Retrieve lock by identifier
     *
     * @param string $identifier
     *
     * @return Lock[]
     */
    public function findByIdentifierPart($identifier)
    {
        $rows = $this->datamapper->getByIdentifierPart($identifier);

        $locks = $this->mapRows($rows);

        return $locks;
    }

    /**
     * Retrieve lock by identifier
     *
     * @param string $identifier
     * @param string $userId
     *
     * @return Lock[]
     */
    public function findByIdentifierPartAndUserId($identifier, $userId)
    {
        $rows = $this->datamapper->getByIdentifierPartAndUserId($identifier, $userId);

        $locks = $this->mapRows($rows);

        return $locks;
    }

    /**
     * Retrieve lock by identifier
     *
     * @param string $identifier
     * @param string $notUserId
     *
     * @return Lock[]
     */
    public function findByIdentifierPartAndOtherUserId($identifier, $notUserId)
    {
        $rows = $this->datamapper->getByIdentifierPartAndNotUserId($identifier, $notUserId);

        $locks = $this->mapRows($rows);

        return $locks;
    }
}