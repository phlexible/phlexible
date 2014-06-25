<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TemplateBundle;

/**
 * Template repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateRepository
{
    /**
     * @var TemplateLoader
     */
    private $loader = null;

    /**
     * @var TemplateCollection
     */
    private $templates = null;

    /**
     * @param TemplateLoader $loader
     */
    public function __construct(TemplateLoader $loader)
    {
        $this->loader = $loader;
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
     * Return a template by ID
     *
     * @param string $templateId
     * @return Template
     */
    public function find($templateId)
    {
        return $this->getCollection()->get($templateId);
    }

    /**
     * Return template by filename
     *
     * @param string $filename
     * @return Template
     */
    public function findByFilename($filename)
    {
        return $this->getCollection()->getByFilename($filename);
    }

    /**
     * Return all templates
     *
     * @return array
     */
    public function getAll()
    {
        return $this->getCollection()->getAll();
    }
}