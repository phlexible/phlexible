<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Mediator;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Sluggable voter interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface SluggableVoterInterface
{
    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isSluggable(TreeNodeInterface $node, $language);
}
