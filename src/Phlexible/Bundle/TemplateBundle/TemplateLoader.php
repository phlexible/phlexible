<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TemplateBundle;

use Symfony\Component\Finder\Finder;

/**
 * Template loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateLoader
{
    /**
     * @var string
     */
    private $templateDir;

    /**
     * @param string $templateDir
     */
    public function __construct($templateDir)
    {
        $this->templateDir = $templateDir;
    }

    /**
     * @return TemplateCollection
     */
    public function loadTemplates()
    {
        $templates = new TemplateCollection();

        $finder = new Finder();
        foreach ($finder->in($this->templateDir)->files() as $templateFile) {
            /* @var $templateFile \Symfony\Component\Finder\SplFileInfo */
            $template = new Template();
            $template
                ->setId(sha1($templateFile->getFilename()))
                ->setName($templateFile->getFilename())
                ->setPath($templateFile->getRelativePath())
                ->setFilename($templateFile->getRelativePathname())
                ->setAbsoluteFilename($templateFile->getPathname());

            $templates->add($template);
        }

        return $templates;
    }
}