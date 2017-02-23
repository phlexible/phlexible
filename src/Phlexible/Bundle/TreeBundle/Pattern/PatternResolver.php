<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\TreeBundle\Pattern;

use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Phlexible\Bundle\SiterootBundle\Entity\Siteroot;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Pattern resolver.
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
     * Resolved page title by configured pattern.
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
        if (!isset($this->patterns[$patternName])) {
            $pattern = '%p';
        } else {
            $pattern = $this->patterns[$patternName];
        }

        return $this->replacePattern($pattern, $siteroot, $elementVersion, $language);
    }

    /**
     * Resolve page title by pattern.
     *
     * @param string         $pattern
     * @param Siteroot       $siteroot
     * @param ElementVersion $elementVersion
     * @param string         $language
     *
     * @return string
     */
    public function replacePattern($pattern, Siteroot $siteroot, ElementVersion $elementVersion, $language)
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
}
