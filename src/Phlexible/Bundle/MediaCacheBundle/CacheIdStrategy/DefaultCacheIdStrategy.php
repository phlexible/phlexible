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
 * Default cache id strategy
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DefaultCacheIdStrategy implements CacheIdStrategyInterface
{
    /**
     * {@inheritdoc}
     */
    public function createCacheId(TemplateInterface $template, FileInterface $file)
    {
        $identifiers = array($template->getKey(), $file->getId(), $file->getVersion());

        return md5(implode('__', $identifiers));
    }
}