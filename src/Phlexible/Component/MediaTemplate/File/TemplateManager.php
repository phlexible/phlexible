<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\File;

use Phlexible\Component\MediaTemplate\Exception\NotFoundException;
use Phlexible\Component\MediaTemplate\Model\TemplateCollection;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateManagerInterface;

/**
 * Media template manager
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateManager implements TemplateManagerInterface
{
    /**
     * @var TemplateLoader
     */
    private $loader;

    /**
     * @var TemplateDumper
     */
    private $dumper;

    /**
     * @var TemplateCollection
     */
    private $templates;

    /**
     * @param TemplateLoader $loader
     * @param TemplateDumper $dumper
     */
    public function __construct(TemplateLoader $loader, TemplateDumper $dumper)
    {
        $this->loader = $loader;
        $this->dumper = $dumper;
    }

    /**
     * @return TemplateCollection
     */
    public function getCollection()
    {
        if ($this->templates === null) {
            $this->templates = $this->loader->loadTemplates();
        }

        return $this->templates;
    }

    /**
     * Find template
     *
     * @param string $key
     *
     * @return TemplateInterface
     * @throws NotFoundException
     */
    public function find($key)
    {
        $template = $this->getCollection()->get($key);

        if ($template !== null) {
            return $template;
        }

        throw new NotFoundException("Media template $key not found.");
    }

    /**
     * @param array $criteria
     *
     * @return TemplateInterface[]
     */
    public function findBy(array $criteria)
    {
        $found = [];
        foreach ($this->getCollection()->all() as $template) {
            foreach ($criteria as $criterium => $value) {
                $method = 'get' . ucfirst(strtolower($criterium));
                if (!method_exists($template, $method)) {
                    continue;
                }
                if ($template->$method() !== $value) {
                    continue 2;
                }
            }

            $found[] = $template;
        }

        return $found;
    }
    /**
     * Return all templates
     *
     * @return TemplateInterface[]
     */
    public function findAll()
    {
        return $this->getCollection()->all();
    }

    /**
     * Update template
     *
     * @param TemplateInterface $template
     */
    public function updateTemplate(TemplateInterface $template)
    {
        $template->setRevision($template->getRevision() + 1);

        $this->dumper->dumpTemplate($template);
    }
}
