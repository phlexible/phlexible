<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Psr\Log\LoggerInterface;

/**
 * Worker logger trait.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
trait WorkerLogger
{
    /**
     * @return LoggerInterface
     */
    abstract protected function getLogger();

    /**
     * Apply error to cache item.
     *
     * @param CacheItem $cacheItem
     * @param string    $status
     * @param string    $message
     * @param string    $inputFilename
     * @param string    $templateType
     * @param string    $templateKey
     * @param string    $logSeverity
     */
    protected function applyError(
        CacheItem $cacheItem,
        $status,
        $message,
        $inputFilename,
        $templateType,
        $templateKey,
        $logSeverity = 'error'
    ) {
        $cacheItem
            ->setCacheStatus($status)
            ->setError($message);

        $logger = $this->getLogger();
        if ($logger && method_exists($logger, $logSeverity)) {
            $logger->$logSeverity($message, array(
                'worker' => get_class($this),
                'templateType' => $templateType,
                'templateKey' => $templateKey,
                'fileId' => $cacheItem->getFileId(),
                'fileVersion' => $cacheItem->getFileVersion(),
                'fileMimeType' => $cacheItem->getMimeType(),
                'fileMediaType' => $cacheItem->getMediaType(),
                'inputFile' => $inputFilename,
            ));
        }
    }
}
