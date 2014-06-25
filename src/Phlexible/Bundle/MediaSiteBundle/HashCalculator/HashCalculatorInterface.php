<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\HashCalculator;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;

/**
 * Hash calculator interface
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
interface HashCalculatorInterface
{
    /**
     * @param FileInterface $file
     * @return string
     */
    public function fromFile(FileInterface $file);

    /**
     * @param FileSourceInterface $fileSource
     * @return string
     */
    public function fromFileSource(FileSourceInterface $fileSource);

    /**
     * @param string $path
     * @return string
     */
    public function fromPath($path);

    /**
     * @param string $string
     * @return string
     */
    public function fromString($string);
}
