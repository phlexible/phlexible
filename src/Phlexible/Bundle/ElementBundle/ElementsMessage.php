<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Elements message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementsMessage extends Message
{
    protected $component = 'elements';
}