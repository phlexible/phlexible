<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\Pattern;

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
     * @param array               $patterns
     * @param string              $projectTitle
     */
    public function __construct(TranslatorInterface $translator, array $patterns, $projectTitle)
    {
        $this->projectTitle = $projectTitle;
        $this->patterns = $patterns;
        $this->translator = $translator;
    }

    /**
     * Return siteroot title
     *
     * @param string         $patternName
     * @param Siteroot       $siteroot
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return string
     */
    public function replace($patternName, Siteroot $siteroot, ElementVersion $elementVersion, $language)
    {
        $pattern = $this->patterns[$patternName];

        $replace = [
            '%s' => $siteroot->getTitle(),
            '%b' => $elementVersion->getBackendTitle($language),
            '%p' => $elementVersion->getPageTitle($language),
            '%n' => $elementVersion->getNavigationTitle($language),
            '%r' => $this->projectTitle,
        ];

        return str_replace(array_keys($replace), array_values($replace), $pattern);
    }
}
