<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;

use Phlexible\Bundle\ElementBundle\Overlay;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Asset controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/asset")
 */
class AssetController extends Controller
{
    /**
     * @var string
     */
    protected $_assetDir = 'Resources/public';

    /**
     * Delivers an icon asset
     *
     * @param Request $request
     *
     * @return Response
     * @Route("/elements/asset", name="elements_asset")
     */
    public function iconAction(Request $request)
    {
        $icon = $request->get('icon', null);
        $params = $request->query->all();

        $overlay = $this->get('phlexible_element.overlay');
        $cacheFilename = $overlay->getAssetPath($icon, $params);

        return $this->get('igorw_file_serve.response_factory')
            ->create(
                $cacheFilename,
                'image/png',
                array(
                    'absolute_path' => true,
                    'inline' => true,
                )
            );
    }

}
