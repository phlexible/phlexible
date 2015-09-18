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
 * Viewable voter interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface ViewableVoterInterface
{
    /**
     * Disable publish check
     */
    public function disablePublishCheck();

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isViewable(TreeNodeInterface $node, $language);
}
