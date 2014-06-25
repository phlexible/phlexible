<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Worker;

use Phlexible\Bundle\MediaCacheBundle\Entity\CacheItem;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;

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
     * @param TemplateInterface $template
     * @param FileInterface     $file
     *
     * @return boolean
     */
    public function accept(TemplateInterface $template, FileInterface $file);

    /**
     * Process template and file
     *
     * @param TemplateInterface $template
     * @param FileInterface     $file
     *
     * @return CacheItem
     */
    public function process(TemplateInterface $template, FileInterface $file);
}