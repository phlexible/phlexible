<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\TreeBundle\Entity\TreeNode;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @return array
     */
    public function getTitles()
    {
        return $this->titles;
    }

    /**
     * @param array $titles
     *
     * @return $this
     */
    public function setTitles($titles)
    {
        $this->titles = $titles;

        return $this;
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getTitle($language = null)
    {
        $language = $language ?: $this->language;
        if (!isset($this->titles[$language])) {
            return "no-title-{$this->getId()}-$language-" . print_r($this->titles, 1);
        }

        return $this->titles[$language];
    }

    /**
     * @return array
     */
    public function getSlugs()
    {
        return $this->slugs;
    }

    /**
     * @param array $slugs
     *
     * @return $this
     */
    public function setSlugs($slugs)
    {
        $this->slugs = $slugs;

        return $this;
    }

    /**
     * @param string $language
     *
     * @return string
     */
    public function getSlug($language)
    {
        $language = $language ?: $this->language;
        if (!isset($this->slugs[$language])) {
            return "no-slug-{$this->getId()}-$language";
        }

        return $this->slugs[$language];
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
