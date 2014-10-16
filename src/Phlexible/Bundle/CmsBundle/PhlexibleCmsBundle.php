<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\CmsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * CMS bundle
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
            echo 'The suhosin php extension is enabled.<br />';
            echo 'Currently there are unresolved issues with phlexible.cms and suhosins "suhosin.post.max_array_index_length" configuration setting.<br />';
            echo 'Exiting now.';
            exit(1);
        }
    }
}
