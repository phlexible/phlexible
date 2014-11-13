<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\File;

use Phlexible\Bundle\MediaTemplateBundle\Exception\NotFoundException;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateCollection;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateManagerInterface;

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
