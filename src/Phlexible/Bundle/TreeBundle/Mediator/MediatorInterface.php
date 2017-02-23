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
 * Mediator interface.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface MediatorInterface
{
    /**
     * @param TreeNodeInterface $node
     *
     * @return bool
     */
    public function accept(TreeNodeInterface $node);

    /**
     * @param TreeNodeInterface $node
     * @param string            $field
     * @param string            $language
     *
     * @return string
     */
    public function getField(TreeNodeInterface $node, $field, $language);

    /**
     * @param TreeNodeInterface $node
     *
     * @return mixed
     */
    public function getContentDocument(TreeNodeInterface $node);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isViewable(TreeNodeInterface $node, $language);

    /**
     * @param TreeNodeInterface $node
     * @param string            $language
     *
     * @return bool
     */
    public function isSluggable(TreeNodeInterface $node, $language);
}
