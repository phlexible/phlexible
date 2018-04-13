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
 * A batch represents a file/template cross combination.
 * Results in a list of queue items.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class Batch
{
    const FILTER_ERROR = 'error';
    const FILTER_MISSING = 'missing';
    const FILTER_UNCACHED = 'uncached';

    /**
     * @var InputDescriptor[]
     */
    private $inputs;

    /**
     * @var TemplateInterface[]
     */
    private $templates;

    /**
     * @var array
     */
    private $flags;

    /**
     * @param InputDescriptor[]   $inputs
     * @param TemplateInterface[] $templates
     * @param array               $flags
     */
    public function __construct(array $inputs = array(), array $templates = array(), array $flags = array())
    {
        $this->inputs = $inputs;
        $this->templates = $templates;
        $this->flags = $flags;
    }

    /**
     * @return InputDescriptor[]
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * @return TemplateInterface[]
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }
}
