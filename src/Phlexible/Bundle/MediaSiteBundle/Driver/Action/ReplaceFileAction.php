<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\FileSource\FileSourceInterface;
use Phlexible\Bundle\MediaSiteBundle\HashCalculator\HashCalculatorInterface;
use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * Replace file action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ReplaceFileAction extends FileAction
{
    /**
     * @var FileSourceInterface
     */
    private $fileSource;

    /**
     * @var HashCalculatorInterface
     */
    private $hashCalculator;

    /**
     * @var AttributeBag
     */
    private $attributes;

    /**
     * @param FileSourceInterface     $fileSource
     * @param FileInterface           $file
     * @param HashCalculatorInterface $hashCalculator
     * @param AttributeBag            $attributes
     * @param \DateTime               $date
     * @param string                  $userId
     */
    public function __construct(
        FileSourceInterface $fileSource,
        FileInterface $file,
        HashCalculatorInterface $hashCalculator,
        AttributeBag $attributes,
        \DateTime $date,
        $userId)
    {
        parent::__construct($file, $date, $userId);

        $this->fileSource = $fileSource;
        $this->hashCalculator = $hashCalculator;
        $this->attributes = $attributes;
    }

    /**
     * @return FileSourceInterface
     */
    public function getFileSource()
    {
        return $this->fileSource;
    }

    /**
     * @return HashCalculatorInterface
     */
    public function getHashCalulator()
    {
        return $this->hashCalculator;
    }

    /**
     * @return AttributeBag
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
