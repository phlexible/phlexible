<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\File\Dumper;

use Phlexible\Bundle\ContentchannelBundle\Entity\Contentchannel;

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
     * @param string         $file
     * @param Contentchannel $contentchannel
     */
    public function dump($file, Contentchannel $contentchannel);
}