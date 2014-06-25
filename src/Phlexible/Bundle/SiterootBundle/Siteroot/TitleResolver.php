<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\SiterootBundle\Siteroot;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Title resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TitleResolver
{
    /**
     * @var string
     */
    private $projectTitle;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     * @param string              $projectTitle
     */
    public function __construct(TranslatorInterface $translator, $projectTitle)
    {
        $this->projectTitle = $projectTitle;
        $this->translator = $translator;
    }

    /**
     * Return siteroot title
     *
     * @param string         $headTitle
     * @param string         $language
     * @param Siteroot       $siteroot
     * @param ElementVersion $elementVersion
     *
     * @return string
     */
    public function replace($headTitle,
                            $language,
                            Siteroot $siteroot,
                            ElementVersion $elementVersion)
    {
        $replace = array(
            '%s' => $siteroot->getTitle(),
            '%b' => $elementVersion->getBackendTitle($language),
            '%p' => $elementVersion->getPageTitle($language),
            '%n' => $elementVersion->getNavigationTitle($language),
            '%r' => $this->projectTitle,
        );

        return str_replace(array_keys($replace), array_values($replace), $headTitle);
    }

    /**
     * @param Siteroot $siteroot
     * @param string   $headTitle
     * @param string   $language
     *
     * @return string
     */
    public function replaceExample(Siteroot $siteroot, $headTitle, $language)
    {
        $replace = array(
            '%s' => $siteroot->getTitle(),
            '%b' => '[' . $this->translator->trans('siteroots.element_backend_title', array(), 'gui', $language) . ']',
            '%p' => '[' . $this->translator->trans('siteroots.element_page_title', array(), 'gui', $language) . ']',
            '%n' => '[' . $this->translator->trans('siteroots.element_navigation_title', array(), 'gui', $language) . ']',
            '%r' => $this->projectTitle,
        );

        return str_replace(array_keys($replace), array_values($replace), $headTitle);
    }

    /**
     * @param string $language
     *
     * @return array
     */
    public function getPlaceholders($language)
    {
        return array(
            array('placeholder' => '%s', 'title' => $this->translator->trans('siteroots.siteroot_title', array(), 'gui', $language)),
            array('placeholder' => '%b', 'title' => $this->translator->trans('siteroots.element_backend_title', array(), 'gui', $language)),
            array('placeholder' => '%p', 'title' => $this->translator->trans('siteroots.element_page_title', array(), 'gui', $language)),
            array('placeholder' => '%n', 'title' => $this->translator->trans('siteroots.element_navigation_title', array(), 'gui', $language)),
            array('placeholder' => '%r', 'title' => $this->translator->trans('siteroots.project_title', array(), 'gui', $language)),
        );
    }
}
