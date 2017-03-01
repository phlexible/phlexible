<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * CMS bundle.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleCmsBundle extends Bundle
{
    const RESOURCE_REPORTS = 'reports';
    const RESOURCE_STATISTICS = 'statistics';

    /**
     * Constructor.
     */
    public function __construct()
    {
        if (extension_loaded('suhosin')) {
            throw new \LogicException('Please deactivate the suhosin extension.');
        }
    }
}
