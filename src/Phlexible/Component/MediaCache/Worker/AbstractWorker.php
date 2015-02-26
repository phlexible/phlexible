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
        $error = $message . PHP_EOL
            . PHP_EOL
            . 'Template type: ' . $template->getType() . PHP_EOL
            . 'Template key: ' . $template->getKey() . PHP_EOL
            . 'File name: ' . $file->getName() . PHP_EOL
            . 'File path: ' . $inputFilename . PHP_EOL
            . 'File ID: ' . $file->getId() . ':' . $file->getVersion() . PHP_EOL
            . 'File type: ' . $file->getMimeType() . PHP_EOL
            . 'File media type: ' . strtolower($file->getMediaType());

        $cacheItem
            ->setCacheStatus($status)
            ->setError($error);
    }
}
