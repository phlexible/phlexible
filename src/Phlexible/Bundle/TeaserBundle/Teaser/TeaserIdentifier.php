<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Teaser;

use Phlexible\Component\Identifier\Identifier;

/**
 * Teasers identifier
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 */
class TeaserIdentifier extends Identifier
{
    /**
     * constructor
     *
     * @param integer $id
     */
    public function __construct($id)
    {
        parent::__construct($id);
    }
}
