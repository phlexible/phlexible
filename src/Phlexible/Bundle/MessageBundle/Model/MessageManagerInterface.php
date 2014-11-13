<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Model;

use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Entity\Message;
use Phlexible\Bundle\MessageBundle\Exception\LogicException;

/**
 * Message manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MessageManagerInterface
{
    /**
     * Find messages
     *
     * @param array $criteria
     * @param null  $orderBy
     * @param null  $limit
     * @param null  $offset
     *
     * @return Message[]
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * Find message
     *
     * @param array $criteria
     * @param null  $orderBy
     *
     * @return Message[]
     */
    public function findOneBy(array $criteria, $orderBy = null);

    /**
     * Find messages by criteria
     *
     * @param Criteria $criteria
     * @param string   $order
     * @param int      $limit
     * @param int      $offset
     *
     * @return Message[]
     */
    public function findByCriteria(Criteria $criteria, $order = null, $limit = null, $offset = null);

    /**
     * @param Criteria $criteria
     *
     * @return int
     */
    public function countByCriteria(Criteria $criteria);

    /**
     * Get priority map
     *
     * @return array
     */
    public function getPriorityNames();

    /**
     * Return type map
     *
     * @return array
     */
    public function getTypeNames();

    /**
     * Return facets
     *
     * @return array
     */
    public function getFacets();

    /**
     * Return facets
     *
     * @param Criteria $criteria
     *
     * @return array
     */
    public function getFacetsByCriteria(Criteria $criteria);

    /**
     * Update message
     *
     * @param Message $message
     *
     * @throws LogicException
     */
    public function updateMessage(Message $message);

    /**
     * Delete message
     *
     * @param Message $message
     */
    public function deleteMessage(Message $message);

}
