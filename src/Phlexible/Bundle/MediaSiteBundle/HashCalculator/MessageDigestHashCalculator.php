<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\HashCalculator;

use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\FileSource\PathSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * Message digest hash calculator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageDigestHashCalculator implements HashCalculatorInterface
{
    /**
     * @var string
     */
    private $algo;

    /**
     * @param string $algo
     */
    public function __construct($algo = 'md5')
    {
        $this->algo = $algo;
    }

    /**
     * {@inheritdoc}
     */
    public function fromFile(FileInterface $file)
    {
        return hash_file('md5', $file->getPhysicalPath());
    }

    /**
     * @param FileSourceInterface $fileSource
     *
     * @throws \RuntimeException
     * @return string
     */
    public function fromFileSource(FileSourceInterface $fileSource)
    {
        if ($fileSource instanceof PathSourceInterface) {
            return $this->fromPath($fileSource->getPath());
        }

        throw new \RuntimeException('Unsupported file source');
    }

    /**
     * {@inheritdoc}
     */
    public function fromPath($path)
    {
        return hash_file('md5', $path);
    }

    /**
     * {@inheritdoc}
     */
    public function fromString($string)
    {
        return hash('md5', $string);
    }
}
