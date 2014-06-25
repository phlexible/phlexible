<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

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
     * @param array         $attributes
     */
    public function __construct(FileInterface $file, array $attributes)
    {
        parent::__construct($file);

        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
