<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract worker
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractWorker implements WorkerInterface
{
    /**
     * Apply error to cache item
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
