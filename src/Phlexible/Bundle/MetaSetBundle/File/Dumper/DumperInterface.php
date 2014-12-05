<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\File\Dumper;

use Phlexible\Bundle\MetaSetBundle\Model\MetaSetInterface;

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
     * @param string           $file
     * @param MetaSetInterface $metaSet
     */
    public function dump($file, MetaSetInterface $metaSet);
}
