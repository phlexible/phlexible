<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\ElementtypeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Data controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elementtypes/data")
 * @Security("is_granted('ROLE_ELEMENTTYPES')")
 */
class DataController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/images", name="elementtypes_data_images")
     */
    public function imagesAction(Request $request)
    {
        $locator = $this->get('pattern_locator');
        $files = $locator->locate('*.gif', 'public/elementtypes', false);
        $prefix = $request->getBasePath() . '/bundles/phlexibleelementtype/elementtypes/';

        foreach ($files as $file) {
            $data[basename($file)] = [
                'title' => basename($file),
                'url'   => $prefix . basename($file)
            ];
        }

        ksort($data);
        $data = array_values($data);

        return new JsonResponse(['images' => $data]);
    }
}
