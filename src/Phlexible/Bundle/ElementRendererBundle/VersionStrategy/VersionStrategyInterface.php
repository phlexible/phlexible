<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementRendererBundle\VersionStrategy;

use Phlexible\Bundle\ElementBundle\Element\Element;
use Phlexible\Bundle\ElementBundle\ElementVersion\ElementVersion;
use Symfony\Component\HttpFoundation\Request;

/**
 * Request version strategy interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface VersionStrategyInterface
{
    /**
     * Return name
     *
     * @return string
     */
    public function getName();

    /**
     * @param Request $request
     * @param Element $element
     * @param array   $languages
     *
     * @return string
     */
    public function findLanguage(Request $request, Element $element, array $languages);

    /**
     * @param Request $request
     * @param Element $element
     * @param string  $language
     *
     * @return ElementVersion
     */
    public function findVersion(Request $request, Element $element, $language);
}
