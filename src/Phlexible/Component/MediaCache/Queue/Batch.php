<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaCache\Queue;

use Doctrine\Common\Collections\ArrayCollection;
use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * A batch represents a file/template cross combination.
 * Results in a list of queue items.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Batch
{
    /**
     * @var ExtendedFileInterface[]|ArrayCollection
     */
    private $files;

    /**
     * @var TemplateInterface[]|ArrayCollection
     */
    private $templates;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
        $this->templates = new ArrayCollection();
    }

    /**
     * Add file
     *
     * @param ExtendedFileInterface $file
     *
     * @return $this
     */
    public function addFile(ExtendedFileInterface $file)
    {
        if (!$this->files->contains($file)) {
            $this->files->add($file);
        }

        return $this;
    }

    /**
     * Add files
     *
     * @param ExtendedFileInterface[] $files
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
     * @param ExtendedFileInterface $file
     *
     * @return $this
     */
    public function removeFile(ExtendedFileInterface $file)
    {
        if ($this->files->contains($file)) {
            $this->files->removeElement($file);
        }

        return $this;
    }

    /**
     * Remove files
     *
     * @param ExtendedFileInterface[] $files
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
     * @return ExtendedFileInterface[]
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
