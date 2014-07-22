<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
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
     * @param CacheItem         $cacheItem
     * @param string            $status
     * @param string            $message
     * @param string            $inputFilename
     * @param TemplateInterface $template
     * @param FileInterface     $file
     */
    protected function applyError(
        CacheItem $cacheItem,
        $status,
        $message,
        $inputFilename,
        TemplateInterface $template,
        FileInterface $file)
    {
        $error = $message . PHP_EOL
            . PHP_EOL
            . 'Template type: ' . $template->getType() . PHP_EOL
            . 'Template key: ' . $template->getKey() . PHP_EOL
            . 'File name: ' . $file->getName() . PHP_EOL
            . 'File path: ' . $inputFilename . PHP_EOL
            . 'File ID: ' . $file->getId() . ':' . $file->getVersion() . PHP_EOL
            . 'File type: ' . $file->getMimeType() . PHP_EOL
            . 'File asset type: ' . strtolower($file->getAttribute('assettype', 'no assettype')) . PHP_EOL
            . 'File document type: ' . strtolower($file->getAttribute('documenttype', 'no documenttype'));

        $cacheItem
            ->setStatus($status)
            ->setError($error);
    }
}
