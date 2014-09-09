<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementFinderBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Element finder message
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementFinderMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array(
            'channel' => 'element_finder',
        );
    }
}