<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\File;

use Phlexible\Component\MediaTemplate\File\Dumper\DumperInterface;
use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Template dumper.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TemplateDumper
{
    /**
     * @var DumperInterface
     */
    private $dumper;

    /**
     * @var string
     */
    private $fileDir;

    /**
     * @param DumperInterface $dumper
     * @param string          $fileDir
     */
    public function __construct(DumperInterface $dumper, $fileDir)
    {
        $this->dumper = $dumper;
        $this->fileDir = $fileDir;
    }

    /**
     * @param TemplateInterface $template
     */
    public function dumpTemplate(TemplateInterface $template)
    {
        $filename = strtolower($template->getKey().'.'.$this->dumper->getExtension());
        $this->dumper->dump($this->fileDir.$filename, $template);
    }
}
