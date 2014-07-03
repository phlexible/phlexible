<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaCacheBundle\Queue;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Bundle\MediaSiteBundle\File;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;

/**
 * A batch represents a file/template cross combination.
 * Results in a list of queue items.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Batch
{
    /**
     * @var FileInterface[]|ArrayCollection
     */
    private $files;

    /**
     * @var TemplateInterface[]|ArrayCollection
     */
    private $templates;

    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->templates = new ArrayCollection();
    }

    /**
     * Add file
     *
     * @param FileInterface $file
     *
     * @return $this
     */
    public function addFile(FileInterface $file)
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }

        return $this;
    }

    /**
     * Add files
     *
     * @param FileInterface[] $files
     *
     * @return $this
     */
    public function addFiles(array $files)
    {
        foreach ($files as $file) {
            $this->addFile($file);
        }

        return $this;
    }

    /**
     * Remove file
     *
     * @param FileInterface $file
     *
     * @return $this
     */
    public function removeFile(FileInterface $file)
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
        }

        return $this;
    }

    /**
     * Remove files
     *
     * @param FileInterface[] $files
     *
     * @return $this
     */
    public function removeFiles(array $files)
    {
        foreach ($files as $file) {
            $this->removeFile($file);
        }

        return $this;
    }

    /**
     * Add template
     *
     * @param TemplateInterface $template
     *
     * @return $this
     */
    public function addTemplate(TemplateInterface $template)
    {
        if (!$this->templates->contains($template)) {
            $this->templates->add($template);
        }

        return $this;
    }

    /**
     * Add templates
     *
     * @param TemplateInterface[] $templates
     *
     * @return $this
     */
    public function addTemplates(array $templates)
    {
        foreach ($templates as $template) {
            $this->addTemplate($template);
        }

        return $this;
    }

    /**
     * @param TemplateInterface $template
     *
     * @return $this
     */
    public function removeTemplate(TemplateInterface $template)
    {
        if ($this->templates->contains($template)) {
            $this->templates->removeElement($template);
        }

        return $this;
    }

    /**
     * @param TemplateInterface[] $templates
     *
     * @return $this
     */
    public function removeTemplates(array $templates)
    {
        foreach ($templates as $template) {
            $this->removeTemplate($template);
        }

        return $this;
    }

    /**
     * @return FileInterface[]
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @return TemplateInterface[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }
}
