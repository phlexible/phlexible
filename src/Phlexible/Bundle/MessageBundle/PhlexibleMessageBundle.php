<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Message bundle
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PhlexibleMessageBundle extends Bundle
{
    const RESOURCE_MESSAGES = 'messages';
    const RESOURCE_MESSAGES_CHANNELS = 'messages_channels';
}
