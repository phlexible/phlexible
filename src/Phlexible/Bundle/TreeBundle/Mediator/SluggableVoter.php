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
 * Sluggable voter
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SluggableVoter implements SluggableVoterInterface
{
    /**
     * {@inheritdoc}
     */
    public function isSluggable(TreeNodeInterface $node, $language)
    {
        return $node->getType() === 'element-full';
    }
}
