<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Siteroot;

use Phlexible\Bundle\SiterootBundle\Entity\Url;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;

/**
 * Siteroot url matcher
 *
 * @author Phillip Look <plook@brainbits.net>
 */
class UrlMatcher
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @param SiterootManagerInterface $siterootManager
     */
    public function __construct(SiterootManagerInterface $siterootManager)
    {
        $this->siterootManager = $siterootManager;
    }

    /**
     * @param string $hostname
     *
     * @throws \Exception
     * @return Url
     */
    public function match($hostname)
    {
        foreach ($this->siterootManager->findAll() as $siteroot) {
            foreach ($siteroot->getUrls() as $siterootUrl) {
                if ($siterootUrl->getHostname() === $hostname) {
                    return $siterootUrl;
                }
            }
        }

        throw new \Exception('Url not found');
    }
}
