<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Phlexible\Bundle\MediaCacheBundle\Model\QueueManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Cache status portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheStatusPortlet extends Portlet
{
    /**
     * @var QueueManagerInterface
     */
    private $queueManager;

    /**
     * @param TranslatorInterface   $translator
     * @param QueueManagerInterface $queueManager
     */
    public function __construct(TranslatorInterface $translator, QueueManagerInterface $queueManager)
    {
        $this
            ->setId('cachestatus-portlet')
            ->setTitle($translator->trans('mediacache.cache_status', array(), 'gui'))
            ->setClass('Phlexible.mediacache.portlet.CacheStatus')
            ->setIconClass('p-mediacache-component-icon')
            ->setResource('mediacache');

        $this->queueManager = $queueManager;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        return $this->queueManager->countAll();
    }
}
