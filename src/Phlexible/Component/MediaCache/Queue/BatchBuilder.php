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

use Phlexible\Component\MediaCache\Worker\InputDescriptor;
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
     * @var InputDescriptor[]
     */
    private $inputs = array();

    /**
     * @var array
     */
    private $flags = array();

    /**
     * @param TemplateInterface[] $templates
     *
     * @return self
     */
    public function templates(array $templates)
    {
        foreach ($templates as $template) {
            $this->template($template);
        }

        return $this;
    }

    /**
     * @param TemplateInterface $template
     *
     * @return self
     */
    public function template(TemplateInterface $template)
    {
        $this->templates[] = $template;

        return $this;
    }

    /**
     * @param InputDescriptor[] $inputs
     *
     * @return self
     */
    public function inputs(array $inputs)
    {
        foreach ($inputs as $input) {
            $this->input($input);
        }

        return $this;
    }

    /**
     * @param InputDescriptor $input
     *
     * @return self
     */
    public function input(InputDescriptor $input)
    {
        $this->inputs[] = $input;

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
        return new Batch($this->inputs, $this->templates, $this->flags);
    }
}
