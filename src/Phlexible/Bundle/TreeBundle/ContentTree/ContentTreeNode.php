<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Cocur\Slugify\Slugify;
use Phlexible\Bundle\TreeBundle\Entity\TreeNode;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;

/**
 * Content tree node
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
