<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Driver\Action;

use Phlexible\Bundle\MediaSiteBundle\Model\AttributeBag;
use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;

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
     * @param AttributeBag    $attributes
     * @param \DateTime       $date
     * @param string          $userId
     */
    public function __construct(FolderInterface $folder, AttributeBag $attributes, \DateTime $date, $userId)
    {
        parent::__construct($folder, $date, $userId);

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
