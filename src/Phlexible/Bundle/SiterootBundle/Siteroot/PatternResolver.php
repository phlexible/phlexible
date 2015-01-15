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
 * Pattern resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PatternResolver
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
     * @param Siteroot       $siteroot
     * @param ElementVersion $elementVersion
     * @param string         $language
     * @param string         $pattern
     *
     * @return string
     */
    public function replace(Siteroot $siteroot, ElementVersion $elementVersion, $language, $pattern)
    {
        $replace = [
            '%s' => $siteroot->getTitle(),
            '%b' => $elementVersion->getBackendTitle($language),
            '%p' => $elementVersion->getPageTitle($language),
            '%n' => $elementVersion->getNavigationTitle($language),
            '%r' => $this->projectTitle,
        ];

        return str_replace(array_keys($replace), array_values($replace), $pattern);
    }

    /**
     * @param Siteroot $siteroot
     * @param string   $language
     * @param string   $pattern
     *
     * @return string
     */
    public function replaceExample(Siteroot $siteroot, $language, $pattern = null)
    {
        $replace = [
            '%s' => $siteroot->getTitle(),
            '%b' => '[' . $this->translator->trans('siteroots.element_backend_title', [], 'gui', $language) . ']',
            '%p' => '[' . $this->translator->trans('siteroots.element_page_title', [], 'gui', $language) . ']',
            '%n' => '[' . $this->translator->trans('siteroots.element_navigation_title', [], 'gui', $language) . ']',
            '%r' => $this->projectTitle,
        ];

        return str_replace(array_keys($replace), array_values($replace), $pattern);
    }

    /**
     * @param string $language
     *
     * @return array
     */
    public function getPlaceholders($language)
    {
        return [
            [
                'placeholder' => '%s',
                'title'       => $this->translator->trans('siteroots.siteroot_title', [], 'gui', $language)
            ],
            [
                'placeholder' => '%b',
                'title'       => $this->translator->trans('siteroots.element_backend_title', [], 'gui', $language)
            ],
            [
                'placeholder' => '%p',
                'title'       => $this->translator->trans('siteroots.element_page_title', [], 'gui', $language)
            ],
            [
                'placeholder' => '%n',
                'title'       => $this->translator->trans('siteroots.element_navigation_title', [], 'gui', $language)
            ],
            [
                'placeholder' => '%r',
                'title'       => $this->translator->trans('siteroots.project_title', [], 'gui', $language)
            ],
        ];
    }
}
