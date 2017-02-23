<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\Model;

/**
 * Template collection.
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
     * Add template.
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
