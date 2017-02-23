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
 * Viewable voter.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ViewableVoter implements ViewableVoterInterface
{
    /**
     * @var bool
     */
    private $checkPublicationState = true;

    /**
     * {@inheritdoc}
     */
    public function disablePublishCheck()
    {
        $this->checkPublicationState = false;
    }

    /**
     * {@inheritdoc}
     */
    public function isViewable(TreeNodeInterface $node, $language)
    {
        $isPublished = true;
        if ($this->checkPublicationState) {
            $isPublished = $node->getTree()->isPublished($node, $language);
        }

        return $isPublished && $node->getInNavigation() && $node->getType() === 'element-full';
    }
}
