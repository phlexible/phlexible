<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Elementtype\File\Writer;

use Phlexible\Component\Elementtype\File\Dumper\XmlDumper;
use Phlexible\Component\Elementtype\Model\Elementtype;
use Symfony\Component\Filesystem\Filesystem;

/**
 * XML writer
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
        $filename = $this->resourceDir . '/' . $elementtype->getId() . '.xml';
        $content = $this->dumper->dump($elementtype);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($filename, $content);

        return $filename;
    }
}
