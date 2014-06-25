<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\CacheIdStrategy;

use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * Cache id strategy interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CacheIdStrategyInterface
{
    /**
     * @param TemplateInterface $template
     * @param FileInterface     $file
     *
     * @return string
     */
    public function createCacheId(TemplateInterface $template, FileInterface $file);
}