<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TemplateBundle;

/**
 * Template collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateCollection
{
    /**
     * @var Template[]
     */
    private $templates = array();

    /**
     * @var array
     */
    private $filenameMap = array();

    /**
     * Add template
     *
     * @param Template $template
     * @return $this
     */
    public function add(Template $template)
    {
        $this->templates[$template->getId()] = $template;
        $this->filenameMap[basename($template->getFilename())] = $template->getId();

        return $this;
    }

    /**
     * @param string $id
     * @return Template
     */
    public function get($id)
    {
        if (isset($this->templates[$id])) {
            return $this->templates[$id];
        }

        return null;
    }

    /**
     * @param string $filename
     * @return Template
     */
    public function getByFilename($filename)
    {
        if (isset($this->filenameMap[$filename])) {
            return $this->get($this->filenameMap[$filename]);
        }

        return null;
    }

    /**
     * @return Template[]
     */
    public function getAll()
    {
        return $this->templates;
    }
}
