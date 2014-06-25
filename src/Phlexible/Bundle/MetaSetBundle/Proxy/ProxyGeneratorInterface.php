<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Proxy;

use Phlexible\Bundle\MetaSetBundle\MetaSet;

/**
 * Proxy generator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ProxyGeneratorInterface
{
    /**
     * @param MetaSet $metaSet
     */
    public function generate(MetaSet $metaSet);
}