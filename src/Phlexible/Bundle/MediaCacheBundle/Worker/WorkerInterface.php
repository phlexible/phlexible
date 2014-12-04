<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
use Phlexible\Component\MediaType\Model\MediaType;

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
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     * @param MediaType             $mediaType
     *
     * @return CacheItem
     */
    public function process(TemplateInterface $template, ExtendedFileInterface $file, MediaType $mediaType);
}
