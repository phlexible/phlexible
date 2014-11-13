<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ContentElement\Dumper;

use Phlexible\Bundle\ElementBundle\ContentElement\ContentElement;

/**
 * Dumper interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * @param ContentElement $contentElement
     */
    public function dump(ContentElement $contentElement);
}
