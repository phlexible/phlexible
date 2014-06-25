<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Folder;

use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;


/**
 * Size calculatur
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SizeCalculator
{
    public function calculate(SiteInterface $site, FolderInterface $folder)
    {
        $totalSize = 0;
        $totalFiles = 0;
        $totalFolders = 0;

        foreach ($site->findFilesByFolder($folder) as $file) {
            $totalSize += $file->getSize();
            $totalFiles++;
        }

        foreach ($site->findFoldersByParentFolder($folder) as $subFolder) {
            $totalFolders++;

            list($size, $files, $folders) = $this->calculate($site, $subFolder);

            $totalSize += $size;
            $totalFiles += $files;
            $totalFolders += $folders;
        }

        return array($totalSize, $totalFiles, $totalFolders);
    }
}
