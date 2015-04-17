<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Twig\Extension;

use Phlexible\Bundle\SiterootBundle\Model\SiterootManagerInterface;
use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootRequestMatcher;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Twig siteroot extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootExtension extends \Twig_Extension
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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param SiterootManagerInterface $siterootManager
     * @param SiterootRequestMatcher   $siterootRequestMatcher
     * @param RequestStack             $requestStack
     */
    public function __construct(
        SiterootManagerInterface $siterootManager,
        SiterootRequestMatcher $siterootRequestMatcher,
        RequestStack $requestStack
    )
    {
        $this->siterootManager = $siterootManager;
        $this->siterootRequestMatcher = $siterootRequestMatcher;
        $this->requestStack = $requestStack;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('special_tid', [$this, 'specialTid']),
        ];
    }

    /**
     * @param string $name
     * @param string $language
     *
     * @return int|null
     */
    public function specialTid($name, $language = null)
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request->attributes->has('siterootUrl')) {
            $siteroot = $request->attributes->get('siterootUrl')->getSiteroot();
        } else {
            $siteroot = $this->siterootRequestMatcher->matchRequest($request);
        }

        if (!$language) {
            $language = $request->getLocale();
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