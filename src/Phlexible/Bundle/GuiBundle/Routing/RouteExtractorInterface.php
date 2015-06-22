<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Route extractor interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RouteExtractorInterface
{
    /**
     * Return routes
     *
     * @param Request $request
     *
     * @return ExtractedRoutes
     */
    public function extract(Request $request);
}
