<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Model;

use Phlexible\Bundle\MediaCacheBundle\Entity\QueueItem;

/**
 * Queue manager interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface QueueManagerInterface
{
    /**
     * @param string $id
     *
     * @return QueueItem
     */
    public function find($id);

    /**
     * @param array    $criteria
     * @param int|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     *
     * @return mixed
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null);

    /**
     * @param int      $fileId
     * @param int|null $fileVersion
     *
     * @return QueueItem[]
     */
    public function findByFile($fileId, $fileVersion = null);

    /**
     * @param string $templateKey
     * @param int    $fileId
     * @param int    $fileVersion
     *
     * @return QueueItem
     */
    public function findByTemplateAndFile($templateKey, $fileId, $fileVersion);

    /**
     * @return int
     */
    public function countAll();

    /**
     * @param QueueItem $queueItem
     */
    public function updateQueueItem(QueueItem $queueItem);

    /**
     * @param QueueItem $queueItem
     */
    public function deleteQueueItem(QueueItem $queueItem);
}
