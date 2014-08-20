<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\ContentTree;

use Phlexible\Bundle\TreeBundle\Model\TreeNode;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Content tree node
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentTreeNode extends TreeNode
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
     * @var array
     */
    private $languages;

    /**
     * @var array
     */
    private $versions;

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
    public function getTitle($language)
    {
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
        if (!isset($this->slugs[$language])) {
            return "no-slug-{$this->getId()}-$language";
        }

        return $this->slugs[$language];
    }

    /**
     * @return array
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * @param array $languages
     *
     * @return $this
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function hasLanguage($language)
    {
        return in_array($language, $this->languages);
    }

    /**
     * @return array
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * @param array $versions
     *
     * @return $this
     */
    public function setVersions(array $versions)
    {
        foreach ($versions as $language => $version) {
            $this->setVersion($language, $version);
        }

        return $this;
    }

    /**
     * @param string $language
     *
     * @return int
     */
    public function getVersion($language)
    {
        if (!$this->hasVersion($language)) {
            return null;
        }

        return $this->versions[$language];
    }

    /**
     * @param string $language
     * @param int    $version
     *
     * @return $this
     */
    public function setVersion($language, $version)
    {
        $this->versions[$language] = (int) $version;

        return $this;
    }

    /**
     * @param string $language
     *
     * @return bool
     */
    public function hasVersion($language)
    {
        return isset($this->versions[$language]);
    }
}
