<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\File;

use Phlexible\Bundle\MediaTemplateBundle\File\Dumper\DumperInterface;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;

/**
 * Template dumper
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
        $filename = strtolower($template->getKey() . '.' . $this->dumper->getExtension());
        $this->dumper->dump($this->fileDir . $filename, $template);
    }
}
