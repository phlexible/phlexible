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

use Phlexible\Component\MediaManager\Volume\ExtendedFileInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Queue batch.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class BatchBuilder
{
    /**
     * @var TemplateInterface[]
     */
    private $templates = array();

    /**
     * @var ExtendedFileInterface[]
     */
    private $files = array();

    /**
     * @var array
     */
    private $flags = array();

    /**
     * Create an empty batch.
     *
     * @return Batch
     */
    public function create()
    {
        return new Batch();
    }

    /**
     * @param TemplateInterface[] $templates
     *
     * @return self
     */
    public function templates(array $templates)
    {
        $this->templates = $templates;

        return $this;
    }

    /**
     * @param ExtendedFileInterface[] $files
     *
     * @return self
     */
    public function files(array $files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return self
     */
    public function filterError()
    {
        $this->flags[] = Batch::FILTER_ERROR;
        $this->flags = array_unique($this->flags);

        return $this;
    }

    /**
     * @return self
     */
    public function filterMissing()
    {
        $this->flags[] = Batch::FILTER_MISSING;
        $this->flags = array_unique($this->flags);

        return $this;
    }

    /**
     * @return self
     */
    public function filterUncached()
    {
        $this->flags[] = Batch::FILTER_UNCACHED;
        $this->flags = array_unique($this->flags);

        return $this;
    }

    /**
     * @return Batch
     */
    public function getBatch()
    {
        return new Batch($this->files, $this->templates, $this->flags);
    }
}
