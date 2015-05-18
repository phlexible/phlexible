<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\AccessControl\Rights;

/**
 * Calculated rights
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @author Marco Fischer <mf@brainbits.net>
 */
class CalculatedRights
{
    private $rights = array();

    /**
     * @param string $language
     * @param array  $row
     */
    public function add($language, array $row)
    {

        if (empty($this->rights[$language])) {
            $this->rights[$language] = array();
        }

        $this->rights[$language] = array_merge(
            $this->rights[$language],
            $row
        );
    }

    /**
     * Return all granted rights
     *
     * @param string $language
     *
     * @return array
     */
    public function getRights($language = null)
    {
        if ($language === null) {
            $language = '_all_';
        }

        $rights = array_key_exists('_all_', $this->rights) ? $this->rights['_all_'] : array();

        if ($language !== '_all_' && !empty($this->rights[$language])) {
            $rights = array_merge($rights, $this->rights[$language]);
        }

        return $rights;
    }


    /**
     * returns if the given right is granted
     *
     * @param string $right
     * @param string $language
     *
     * @return bool
     */
    public function hasRight($right = '', $language = null)
    {
        if (!count($this->rights)) {
            return false;
        }

        if ($language === null) {
            $result = array_key_exists($right, $this->rights['_all_']);
        } else {
            $result = (array_key_exists($language, $this->rights) && array_key_exists($right, $this->rights[$language])) ||
                (array_key_exists('_all_', $this->rights) && array_key_exists($right, $this->rights['_all_']));
        }

        return $result;
    }
}
