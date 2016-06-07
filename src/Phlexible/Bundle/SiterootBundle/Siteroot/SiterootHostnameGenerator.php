<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Siteroot;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;

/**
 * Siteroot hostname generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootHostnameGenerator
{
    /**
     * @var array
     */
    private $urlMappings;

    /**
     * @param array $urlMappings
     */
    public function __construct(array $urlMappings)
    {
        $this->urlMappings = $urlMappings;
    }

    /**
     * @param Siteroot $siteroot
     * @param string   $language
     *
     * @return string
     */
    public function generate(Siteroot $siteroot, $language)
    {
        $defaultSiteroot = null;

        $siterootUrl = $siteroot->getDefaultUrl($language);
        $hostname = $siterootUrl->getHostname();

        if (isset($this->urlMappings[$hostname])) {
            $hostname = $this->urlMappings[$hostname];
        }

        return $hostname;
    }
}
