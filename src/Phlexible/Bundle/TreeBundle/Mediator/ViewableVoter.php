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
 * Viewable voter
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

        return $isPublished && $node->getType() === 'element-full';
    }
}
