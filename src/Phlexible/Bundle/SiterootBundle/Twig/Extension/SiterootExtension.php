<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Twig\Extension;

use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootRequestMatcher;
use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootsAccessor;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig siteroot extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var SiterootManagerInterface
     */
    private $siterootManager;

    /**
     * @var SiterootRequestMatcher
     */
    private $siterootRequestMatcher;

    /**
     * @var SiterootsAccessor
     */
    private $siterootsAccessor;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param SiterootManagerInterface $siterootManager
     * @param SiterootRequestMatcher   $siterootRequestMatcher
     * @param SiterootsAccessor        $siterootsAccessor
     * @param RequestStack             $requestStack
     */
    public function __construct(
        SiterootManagerInterface $siterootManager,
        SiterootRequestMatcher $siterootRequestMatcher,
        SiterootsAccessor $siterootsAccessor,
        RequestStack $requestStack
    )
    {
        $this->siterootManager = $siterootManager;
        $this->siterootRequestMatcher = $siterootRequestMatcher;
        $this->siterootsAccessor = $siterootsAccessor;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('special_tid', [$this, 'specialTid']),
            new \Twig_SimpleFunction('current_siteroot', [$this, 'currentSiteroot']),
        ];
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'siteroots' => $this->siterootsAccessor
        );
    }

    /**
     * @return Siteroot
     */
    public function currentSiteroot()
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request->attributes->has('siterootUrl')) {
            $siteroot = $request->attributes->get('siterootUrl')->getSiteroot();
        } else {
            $masterRequest = $this->requestStack->getMasterRequest();
            if ($masterRequest !== $request && $masterRequest->attributes->has('siterootUrl')) {
                $siteroot = $masterRequest->attributes->get('siterootUrl')->getSiteroot();
            } else {
                $siteroot = $this->siterootRequestMatcher->matchRequest($request);
            }
        }

        return $siteroot;
    }

    /**
     * @param string $name
     * @param string $language
     *
     * @return int|null
     */
    public function specialTid($name, $language = null)
    {
        $siteroot = $this->currentSiteroot();

        if (!$siteroot) {
            throw new \LogicException("Siteroot could not be determined.");
        }

        if (!$language) {
            $language = $this->requestStack->getCurrentRequest()->getLocale();
        }

        return $siteroot->getSpecialTid($language, $name);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'phlexible_siteroot';
    }
}
