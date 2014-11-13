<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver;

use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;

/**
 * Abstract driver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
abstract class AbstractDriver implements DriverInterface
{
    /**
     * @var SiteInterface
     */
    private $site;

    /**
     * {@inheritdoc}
     */
    public function setSite(SiteInterface $site)
    {
        $this->site = $site;
    }

    /**
     * {@inheritdoc}
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * {@inheritdoc}
     */
    public function getFeatures()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function hasFeature($name)
    {
        return in_array($name, $this->getFeatures());
    }
}
