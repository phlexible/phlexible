<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaTemplate\File\Dumper;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;

/**
 * Dumper interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface DumperInterface
{
    /**
     * Return supported extension
     *
     * @return string
     */
    public function getExtension();

    /**
     * @param string            $file
     * @param TemplateInterface $template
     */
    public function dump($file, TemplateInterface $template);
}
