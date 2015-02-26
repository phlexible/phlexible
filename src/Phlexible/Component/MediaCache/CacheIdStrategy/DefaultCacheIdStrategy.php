<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaCache\CacheIdStrategy;

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

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
    public function createCacheId(TemplateInterface $template, ExtendedFileInterface $file)
    {
        $identifiers = [$template->getKey(), $file->getId(), $file->getVersion(), $file->getHash()];

        return md5(implode('__', $identifiers));
    }
}
