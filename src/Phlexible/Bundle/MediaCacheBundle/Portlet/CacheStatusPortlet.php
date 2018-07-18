<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaCacheBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Component\MediaCache\Domain\CacheItem;
use Phlexible\Component\MediaCache\Model\CacheManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Cache status portlet.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheStatusPortlet extends Portlet
{
    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @param TranslatorInterface   $translator
     * @param CacheManagerInterface $cacheManager
     */
    public function __construct(TranslatorInterface $translator, CacheManagerInterface $cacheManager)
    {
        $this
            ->setId('cachestatus-portlet')
            ->setTitle($translator->trans('mediacache.cache_status', [], 'gui'))
            ->setClass('Phlexible.mediacache.portlet.CacheStatus')
            ->setIconClass('p-mediacache-component-icon')
            ->setRole('ROLE_MEDIA_CACHE');

        $this->cacheManager = $cacheManager;
    }

    /**
     * Return Portlet data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->cacheManager->countBy(array('queueStatus' => CacheItem::QUEUE_WAITING));
    }
}
