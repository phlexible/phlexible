<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Routing;

use Symfony\Component\HttpFoundation\Request;

/**
 * Route extractor interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface RouteExtractorInterface
{
    /**
     * Return routes.
     *
     * @param Request $request
     *
     * @return ExtractedRoutes
     */
    public function extract(Request $request);
}
