<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaSiteBundle\Folder;

use Phlexible\Bundle\MediaSiteBundle\Model\FolderInterface;
use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;


/**
 * Size calculatur
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class SizeCalculator
{
    /**
     * @param SiteInterface   $site
     * @param FolderInterface $folder
     *
     * @return CalculatedSize
     */
    public function calculate(SiteInterface $site, FolderInterface $folder = null)
    {
        if (null === $folder) {
            $folder = $site->findRootFolder();
        }

        $size = 0;
        $numFiles = 0;

        foreach ($site->findFilesByFolder($folder) as $file) {
            $size += $file->getSize();
            $numFiles++;
        }

        $calculatedsize = new CalculatedSize($size, 1, $numFiles);

        foreach ($site->findFoldersByParentFolder($folder) as $subFolder) {
            $subCalculatedSize = $this->calculate($site, $subFolder);
            $calculatedsize->merge($subCalculatedSize);
        }

        return $calculatedsize;
    }
}
