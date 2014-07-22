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
 * Create folder action
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CreateFolderAction extends Action
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var FolderInterface
     */
    private $targetFolder;

    /**
     * @var AttributeBag
     */
    private $attributes;

    /**
     * @param string          $name
     * @param FolderInterface $targetFolder
     * @param AttributeBag    $attributes
     * @param \DateTime       $date
     * @param string          $userId
     */
    public function __construct($name, FolderInterface $targetFolder, AttributeBag $attributes, \DateTime $date, $userId)
    {
        parent::__construct($date, $userId);

        $this->name = $name;
        $this->targetFolder = $targetFolder;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return FolderInterface
     */
    public function getTargetFolder()
    {
        return $this->targetFolder;
    }

    /**
     * @return AttributeBag
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
}
