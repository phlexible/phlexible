<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
