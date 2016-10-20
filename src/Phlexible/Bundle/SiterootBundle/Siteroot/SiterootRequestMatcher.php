<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

        $hostname = $request->getHost();

        foreach ($this->siterootManager->findAll() as $siteroot) {
            foreach ($siteroot->getUrls() as $siterootUrl) {
                $siterootHostname = $siterootUrl->getHostname();
                if ($siterootHostname === $hostname) {
                    return $siteroot;
                }
                if (isset($this->urlMappings[$siterootHostname])) {
                    if (in_array($hostname, $this->urlMappings[$siterootHostname])) {
                        return $siteroot;
                    }
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
