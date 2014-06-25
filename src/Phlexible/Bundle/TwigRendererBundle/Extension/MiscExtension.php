<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TwigRendererBundle\Extension;

/**
 * Twig url extension
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MiscExtension extends \Twig_Extension
{
    private static $id = 0;

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('id', array($this, 'id')),
        );
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public function id($prefix = '')
    {
        // raise id
        $id = ++self::$id;

        if ($prefix) {
            $id = $prefix . $id;
        }

        return $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'misc';
    }
}