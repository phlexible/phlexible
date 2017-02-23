<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Component\Volume\Folder\SizeCalculator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Status controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/status/media")
 * @Security("is_granted('ROLE_SUPER_ADMIN')")
 */
class StatusController extends Controller
{
    /**
     * Show site status.
     *
     * @return Response
     * @Route("", name="mediamanager_status")
     */
    public function indexAction()
    {
        $volumes = $this->get('phlexible_media_manager.volume_manager')->all();

        $out = '<pre>';

        foreach ($volumes as $volumeKey => $volume) {
            $sizeCalculator = new SizeCalculator();
            $calculatedSize = $sizeCalculator->calculate($volume, $volume->findRootFolder());

            $out .= 'Volume: '.$volumeKey.PHP_EOL;
            //            $out .= '  Driver:   ' . $site->getDriver() . PHP_EOL;
            $out .= '  ID:       '.$volume->getId().PHP_EOL;
            $out .= '  Quota:    '.$volume->getQuota().PHP_EOL;
            $out .= '  Size:     '.$calculatedSize->getSize().PHP_EOL;
            $out .= '  Folders:  '.$calculatedSize->getNumFolders().PHP_EOL;
            $out .= '  Files:    '.$calculatedSize->getNumFiles().PHP_EOL;
            $out .= '  RootDir:  '.$volume->getRootDir().PHP_EOL;
            $out .= '    exists:   '.(file_exists($volume->getRootDir()) ? 'OK' : 'Not OK').PHP_EOL;
            $out .= '    readable: '.(is_readable($volume->getRootDir()) ? 'OK' : 'Not OK').PHP_EOL;
            $out .= '    writable: '.(is_writable($volume->getRootDir()) ? 'OK' : 'Not OK').PHP_EOL;
            $out .= PHP_EOL;
        }

        return new Response($out);
    }
}
