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
 * Selectfield Controller
 *
 * @author Matthias Harmuth <mharmuth@brainbits.net>
 * @Route("/elementtypes/selectfield")
 */
class SelectfieldController extends Controller
{
    /**
     * Return available functions
     *
     * @return JsonResponse
     * @Route("/select", name="elementtypes_selectfield_providers")
     * @Security("is_granted('ROLE_ELEMENTTYPES')")
     */
    public function selectAction()
    {
        $selectFieldProviders = $this->get('phlexible_elementtype.select_field_providers');

        $data = [];
        foreach ($selectFieldProviders->all() as $selectFieldProvider) {
            $data[] = [
                'name'  => $selectFieldProvider->getName(),
                'title' => $selectFieldProvider->getTitle($this->getUser()->getInterfaceLanguage('en')),
            ];
        }

        return new JsonResponse(['functions' => $data]);
    }

    /**
     * Return selectfield data for lists
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/function", name="elementtypes_selectfield_function")
     * @Security("is_granted('ROLE_ELEMENTS')")
     */
    public function functionAction(Request $request)
    {
        $selectFieldProviders = $this->get('phlexible_elementtype.select_field_providers');

        $providerName = $request->get('provider');
        $siterootId = $request->get('siterootId');
        $language = $request->get('language');
        $interfaceLanguage = $this->getUser()->getInterfaceLanguage('en');

        $provider = $selectFieldProviders->get($providerName);
        $data = $provider->getData($siterootId, $interfaceLanguage, $language);

        return new JsonResponse(['data' => $data]);
    }
}
