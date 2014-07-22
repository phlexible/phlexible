<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FileInterface;

/**
 * Set file attributes action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SetFileAttributesAction extends FileAction
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @param FileInterface $file
     * @param AttributeBag  $attributes
     * @param \DateTime     $date
     * @param string        $userId
     */
    public function __construct(FileInterface $file, AttributeBag $attributes, \DateTime $date, $userId)
    {
        parent::__construct($file, $date, $userId);

        $this->attributes = $attributes;
    }

    /**
     * @return AttributeBag
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
