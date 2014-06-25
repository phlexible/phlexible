<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\MetaSet;

use Phlexible\Bundle\MetaSetBundle\Dumper\DumperInterface;

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
     * @param MetaSet $metaSet
     */
    public function dumpTemplate(MetaSet $metaSet)
    {
        $filename = strtolower($metaSet->getTitle() . '.' . $this->dumper->getExtension());
        $this->dumper->dump($this->fileDir . $filename, $metaSet);
    }
}