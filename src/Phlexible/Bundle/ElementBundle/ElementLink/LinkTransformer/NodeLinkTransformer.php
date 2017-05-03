<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementBundle\ElementLink\LinkTransformer;

use Phlexible\Bundle\ElementBundle\Entity\ElementLink;
use Phlexible\Bundle\TreeBundle\ContentTree\ContentTreeManagerInterface;

/**
 * Node link transformer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class NodeLinkTransformer implements LinkTransformerInterface
{
    /**
     * @var ContentTreeManagerInterface
     */
    private $treeManager;

    /**
     * @param ContentTreeManagerInterface $treeManager
     */
    public function __construct(ContentTreeManagerInterface $treeManager)
    {
        $this->treeManager = $treeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ElementLink $elementLink)
    {
        return in_array($elementLink->getType(), ['link-internal']);
    }

    /**
     * {@inheritdoc}
     */
    public function transform(ElementLink $elementLink, array $data)
    {
        if ($elementLink->getType() === 'link-internal') {
            $tree = $this->treeManager->findByTreeId($elementLink->getTarget());
            if ($tree) {
                $node = $tree->get($elementLink->getTarget());
                if ($node) {
                    $name = sprintf(
                        '%s [%s]',
                        $node->getTitle($elementLink->getLanguage()),
                        $elementLink->getTarget()
                    );
                    $data['content'] = $name;
                    $data['payload']['name'] = $name;
                    $data['payload']['nodeId'] = $node->getId();
                    $data['payload']['type'] = $node->getType();
                    $data['payload']['typeId'] = $node->getTypeId();
                }
            }

            $data['iconCls']= 'p-element-page-icon';
        }

        return $data;
    }
}
