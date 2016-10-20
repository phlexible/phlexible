<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\Volume\Event;

use Phlexible\Component\Volume\Model\FolderInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Abstract folder event
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class FolderEvent extends Event
{
    /**
     * @var FolderInterface
     */
    private $folder;

    /**
     * @param FolderInterface $folder
     */
    public function __construct(FolderInterface $folder)
    {
        $this->folder = $folder;
    }

    /**
     * @return FolderInterface
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
