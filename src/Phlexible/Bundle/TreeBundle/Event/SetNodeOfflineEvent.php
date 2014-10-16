<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Event;

use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;

/**
 * Set node offline event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SetNodeOfflineEvent extends NodeEvent
{
    /**
     * @var string
     */
    private $language;

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     */
    public function __construct(TreeNodeInterface $node, $language)
    {
        parent::__construct($node);

        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }
}
