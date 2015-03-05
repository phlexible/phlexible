<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Siteroot;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Siteroot request matcher
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootRequestMatcher
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var array
     */
    private $urlMappings;

    /**
     * @param SiterootManagerInterface $siterootManager
     * @param array                    $urlMappings
     */
    public function __construct(SiterootManagerInterface $siterootManager, array $urlMappings)
    {
        $this->siterootManager = $siterootManager;
        $this->urlMappings = $urlMappings;
    }

    /**
     * @param Request $request
     *
     * @return Siteroot
     */
    public function matchRequest(Request $request)
    {
        $defaultSiteroot = null;

        $hostname = $request->getHttpHost();

        foreach ($this->siterootManager->findAll() as $siteroot) {
            foreach ($siteroot->getUrls() as $siterootUrl) {
                $siterootHostname = $siterootUrl->getHostname();
                if (isset($this->urlMappings[$hostname])) {
                    $siterootHostname = $this->urlMappings[$hostname];
                }
                if ($siterootHostname === $hostname) {
                    return $siteroot;
                }
                if ($siteroot->isDefault()) {
                    $defaultSiteroot = $siteroot;
                }
            }
        }

        if ($defaultSiteroot) {
            return $defaultSiteroot;
        }

        return null;
    }
}
