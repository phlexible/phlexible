<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\File;

use Phlexible\Bundle\ContentchannelBundle\Entity\Contentchannel;
use Phlexible\Bundle\ContentchannelBundle\File\Dumper\DumperInterface;

/**
 * Content channel dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ContentchannelDumper
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
     * @param Contentchannel $contentchannel
     */
    public function dump(Contentchannel $contentchannel)
    {
        $filename = strtolower($contentchannel->getId() . '.' . $this->dumper->getExtension());
        $this->dumper->dump($this->fileDir . $filename, $contentchannel);
    }
}