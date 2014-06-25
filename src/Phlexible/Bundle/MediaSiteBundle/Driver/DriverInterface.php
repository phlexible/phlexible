<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver;

use Phlexible\Bundle\MediaSiteBundle\Driver\Action\ActionInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;

/**
 * Driver interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DriverInterface extends FindInterface
{
    const FEATURE_VERSIONS = 'versions';

    /**
     * @param SiteInterface $site
     *
     * @return $this
     */
    public function setSite(SiteInterface $site);

    /**
     * @return SiteInterface
     */
    public function getSite();

    /**
     * @return array
     */
    public function getFeatures();

    /**
     * @param ActionInterface $action
     */
    public function execute(ActionInterface $action);
}