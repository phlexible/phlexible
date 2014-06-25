<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\AbstractPortlet;
use Phlexible\Bundle\MediaCacheBundle\Model\QueueManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Cache status portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CacheStatusPortlet extends AbstractPortlet
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
        $this->id        = 'cachestatus-portlet';
        $this->title     = $translator->trans('mediacache.cache_status', array(), 'gui');
        $this->class     = 'Phlexible.mediacache.portlet.CacheStatus';
        $this->iconClass = 'p-mediacache-component-icon';
        $this->resource  = 'mediacache';

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
