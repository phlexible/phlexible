<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Templating\Asset;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Templating\Asset\PathPackage as BasePathPackage;

/**
 * Path package
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PathPackage extends BasePathPackage
{
    /**
     * Constructor.
     *
     * @param Request $request The current request
     * @param string  $path    The path
     * @param string  $version The version
     * @param string  $format  The version format
     */
    public function __construct(Request $request, $path, $version = null, $format = null)
    {
        parent::__construct($request->getBasePath() . $path, $version, $format);
    }
}
