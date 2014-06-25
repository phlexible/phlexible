<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendBundle;

use Phlexible\Bundle\MessageBundle\Entity\Message;

/**
 * Frontend message
 *
 * @author Marcus Stöhr <mstoehr@brainbits.net>
 */
class FrontendMessage extends Message
{
    /**
     * {@inheritdoc}
     */
    public function getDefaults()
    {
        return array(
            'channel' => 'frontend',
        );
    }
}