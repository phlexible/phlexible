<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Media manager bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMediaManagerBundle extends Bundle
{
    const RESOURCE_MEDIA = 'media';
    const RESOURCE_MEDIA_ADMINISTRATION = 'media_administration';
}
