<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\File;

use Phlexible\Bundle\MetaSetBundle\File\Dumper\DumperInterface;
use Phlexible\Bundle\MetaSetBundle\Model\MetaSetInterface;

/**
 * Meta set dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MetaSetDumper
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
     * @param MetaSetInterface $metaSet
     */
    public function dumpMetaSet(MetaSetInterface $metaSet)
    {
        $filename = strtolower($metaSet->getId() . '.' . $this->dumper->getExtension());
        $this->dumper->dump($this->fileDir . $filename, $metaSet);
    }
}
