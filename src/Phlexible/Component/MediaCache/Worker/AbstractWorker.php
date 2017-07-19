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

use Phlexible\Component\MediaCache\Domain\CacheItem;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract worker.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractWorker implements WorkerInterface
{
    /**
     * @return LoggerInterface
     */
    abstract protected function getLogger();

    /**
     * Apply error to cache item.
     *
     * @param CacheItem             $cacheItem
     * @param string                $status
     * @param string                $message
     * @param string                $inputFilename
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     */
    protected function applyError(
        CacheItem $cacheItem,
        $status,
        $message,
        $inputFilename,
        TemplateInterface $template,
        ExtendedFileInterface $file)
    {
        $cacheItem
            ->setCacheStatus($status)
            ->setError($message);

        $this->getLogger()->error($message, array(
            'worker' => get_class($this),
            'templateType' => $template->getType(),
            'templateKey' => $template->getKey(),
            'fileName' => $file->getName(),
            'filePath' => $inputFilename,
            'fileId' => $file->getId(),
            'fileVersion' => $file->getVersion(),
            'fileMimeType' => $file->getMimeType(),
            'fileMediaType' => $file->getMediaType(),
        ));
    }
}
