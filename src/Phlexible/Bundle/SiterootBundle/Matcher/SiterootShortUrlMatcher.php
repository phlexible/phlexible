<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\Request\Matcher;

use Phlexible\Bundle\SiterootBundle\Siteroot\SiterootRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

/**
 * Request matcher interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SiterootShortUrlMatcher implements RequestMatcherInterface
{
    /**
     * @var SiterootRepository
     */
    private $siterootRepository;

    /**
     * @param SiterootRepository $siterootRepository
     */
    public function __construct(SiterootRepository $siterootRepository)
    {
        $this->siterootRepository = $siterootRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
        #RenderRequest $renderRequest,
        #SiterootUrl $fallbackSiterootUrl = null)
    {
        if ($renderRequest->getTid() === null) {
            $path = $renderRequest->getPath();

            if (!$path || $path === '/') {
                return;
            }

            foreach ($this->siterootRepository->findAll() as $siteroot) {
                foreach ($siteroot->getShortUrls() as $shortUrl) {
                    if ($shortUrl->getPath() === $path) {
                        $renderRequest->setTid($shortUrl->getTarget());
                        $renderRequest->setLanguage($shortUrl->getLanguage());
                        $renderRequest->setSiteroot($siteroot);
                        $renderRequest->setSiterootUrl($siteroot->getDefaultUrl($renderRequest->getLanguage()));

                        return;
                    }
                }
            }
        }
    }
}
