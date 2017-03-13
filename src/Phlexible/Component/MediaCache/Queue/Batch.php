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
     * @var ExtendedFileInterface[]
     */
    private $files;

    /**
     * @var TemplateInterface[]
     */
    private $templates;

    /**
     * @var array
     */
    private $flags;

    /**
     * @param ExtendedFileInterface[] $files
     * @param TemplateInterface[]     $templates
     * @param array                   $flags
     */
    public function __construct(array $files = array(), array $templates = array(), array $flags = array())
    {
        $this->files = $files;
        $this->templates = $templates;
        $this->flags = $flags;
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

    /**
     * @return array
     */
    public function getFlags()
    {
        return $this->flags;
    }
}
