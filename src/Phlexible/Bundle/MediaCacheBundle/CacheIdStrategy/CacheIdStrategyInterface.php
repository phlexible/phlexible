<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\CacheIdStrategy;

use Phlexible\Bundle\MediaManagerBundle\Volume\ExtendedFileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;

/**
 * Cache id strategy interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface CacheIdStrategyInterface
{
    /**
     * @param TemplateInterface     $template
     * @param ExtendedFileInterface $file
     *
     * @return string
     */
    public function createCacheId(TemplateInterface $template, ExtendedFileInterface $file);
}
