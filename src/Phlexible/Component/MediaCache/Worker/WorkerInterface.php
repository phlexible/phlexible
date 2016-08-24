<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;
use Psr\Log\LoggerInterface;

/**
 * Cache worker interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface WorkerInterface
{
    /**
     * Are the given template and asset supported?
     *
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return bool
     */
    public function accept(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType);

    /**
     * Process template and file
     *
     * @param CacheItem             $cacheItem
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     */
    public function process(CacheItem $cacheItem, TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType);

    /**
     * @return LoggerInterface
     */
    public function getLogger();
}
