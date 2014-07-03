<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\ElementVersionData;

use Phlexible\Bundle\ElementBundle\ElementVersion\ElementVersion;

/**
 * Element version data
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marcus Stöhr <mstoehr@brainbits.net>
 */
class ElementVersionData
{
    /**
     * @var ElementVersion
     */
    private $elementVersion;

    /**
     * @var string
     */
    private $language;

    /**
     * @var array
     */
    private $dataTree;

    /**
     * @var array
     */
    private $fieldClasses = array();

    /**
     * @param bool $skipRoot
     *
     * @return array
     */
    public function getTree($skipRoot = false)
    {
        if ($skipRoot) {
            return $this->dataTree['children'];
        }

        return $this->dataTree;
    }

    /**
     * @param array $tree
     * @param bool  $skipRoot
     *
     * @return $this
     */
    public function setTree(array $tree, $skipRoot = false)
    {
        if ($skipRoot) {
            $this->dataTree['children'] = $tree;
        } else {
            $this->dataTree = $tree;
        }

        return $this;
    }

    /**
     * @return ElementVersion
     */
    public function getElementVersion()
    {
        return $this->elementVersion;
    }

    /**
     * @param ElementVersion $elementVersion
     *
     * @return $this
     */
    public function setElementVersion($elementVersion)
    {
        $this->elementVersion = $elementVersion;

        return $this;
    }

    /**
     * @return array
     */
    public function getFieldClasses()
    {
        return $this->fieldClasses;
    }

    /**
     * @param array $fieldClasses
     *
     * @return $this
     */
    public function setFieldClasses($fieldClasses)
    {
        $this->fieldClasses = $fieldClasses;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

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
}
