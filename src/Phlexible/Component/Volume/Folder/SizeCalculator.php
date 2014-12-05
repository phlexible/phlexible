<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\Volume\Folder;

use Phlexible\Component\Volume\Model\FolderInterface;
use Phlexible\Component\Volume\VolumeInterface;

/**
 * Size calculatur
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SizeCalculator
{
    /**
     * @param VolumeInterface $volume
     * @param FolderInterface $folder
     *
     * @return CalculatedSize
     */
    public function calculate(VolumeInterface $volume, FolderInterface $folder = null)
    {
        if (null === $folder) {
            $folder = $volume->findRootFolder();
        }

        $size = 0;
        $numFiles = 0;

        foreach ($volume->findFilesByFolder($folder) as $file) {
            $size += $file->getSize();
            $numFiles++;
        }

        $calculatedsize = new CalculatedSize($size, 1, $numFiles);

        foreach ($volume->findFoldersByParentFolder($folder) as $subFolder) {
            $subCalculatedSize = $this->calculate($volume, $subFolder);
            $calculatedsize->merge($subCalculatedSize);
        }

        return $calculatedsize;
    }
}
