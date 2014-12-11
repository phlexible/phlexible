<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\Model;

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
        if ($this->has($key)) {
            return $this->templates[(string) $key];
        }

        return null;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return isset($this->templates[(string) $key]);
    }

    /**
     * @return TemplateInterface[]
     */
    public function all()
    {
        return $this->templates;
    }
}
