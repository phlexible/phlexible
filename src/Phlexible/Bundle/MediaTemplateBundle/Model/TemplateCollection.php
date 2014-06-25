<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\Model;

/**
 * Template collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateCollection
{
    /**
     * @var TemplateInterface[]
     */
    private $templates = array();

    /**
     * Add template
     *
     * @param TemplateInterface $template
     *
     * @return $this
     */
    public function add(TemplateInterface $template)
    {
        $this->templates[$template->getKey()] = $template;

        return $this;
    }

    /**
     * @param string $key
     *
     * @return TemplateInterface
     */
    public function get($key)
    {
        if (isset($this->templates[$key])) {
            return $this->templates[$key];
        }

        return null;
    }

    /**
     * @return TemplateInterface[]
     */
    public function getAll()
    {
        return $this->templates;
    }
}
