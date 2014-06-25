<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\Folder\FolderInterface;

/**
 * Set folder attributes action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SetFolderAttributesAction extends FolderAction
{
    /**
     * @var array
     */
    private $attributes;

    /**
     * @param FolderInterface $folder
     * @param array           $attributes
     */
    public function __construct(FolderInterface $folder, array $attributes)
    {
        parent::__construct($folder);

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
