<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Cocur\Slugify\Slugify;
use Phlexible\Bundle\TreeBundle\Entity\TreeNode;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

/**
 * Content tree node.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentTreeNode extends TreeNode implements RouteObjectInterface
{
    /**
     * @var array
     */
    private $titles;

    /**
     * @var array
     */
    private $slugs;

    /**
     * @var string
     */
    private $language;

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getTitle($language = null)
    {
        return $this->getField('navigation', $language);
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getSlug($language = null)
    {
        $slugify = new Slugify();

        return $slugify->slugify($this->getTitle($language));
    }

    /**
     * @param string $field
     * @param string $language
     *
     * @return string
     */
    public function getField($field, $language = null)
    {
        $language = $language ?: $this->language;

        return $this->getTree()->getField($this, $field, $language);
    }

    /**
     * @param string $language
     *
     * @return \DateTime
     */
    public function getPublishedAt($language = null)
    {
        return $this->getTree()->getPublishedAt($this, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteKey()
    {
        return $this->getId();
    }
}
