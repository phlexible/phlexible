<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\File\Writer;

use Phlexible\Bundle\ElementtypeBundle\File\Dumper\XmlDumper;
use Phlexible\Bundle\ElementtypeBundle\Model\Elementtype;
use Symfony\Component\Filesystem\Filesystem;

/**
 * XML writer.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlWriter implements WriterInterface
{
    /**
     * @var XmlDumper
     */
    private $dumper;

    /**
     * @var string
     */
    private $resourceDir;

    /**
     * @param XmlDumper $dumper
     * @param string    $resourceDir
     */
    public function __construct(XmlDumper $dumper, $resourceDir)
    {
        $this->dumper = $dumper;
        $this->resourceDir = $resourceDir;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Elementtype $elementtype)
    {
        $filename = $this->resourceDir.'/'.$elementtype->getId().'.xml';
        $content = $this->dumper->dump($elementtype);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($filename, $content);

        return $filename;
    }
}
