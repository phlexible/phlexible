<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MetaSetBundle\MetaSet;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sets controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/metasets/sets")
 * @Security("is_granted('metasets')")
 */
class SetsController extends Controller
{
    /**
     * List sets
     *
     * @return JsonResponse
     * @Route("/list", name="metasets_sets_list")
     */
    public function listAction()
    {
        $repository = $this->get('metasets.repository');
        $sets = $repository->findAll();

        $data = array();
        foreach ($sets as $set) {
            $data[] = array(
                'id'    => $set->getId(),
                'title' => $set->getTitle(),
            );
        }

        return new JsonResponse(array('sets'=> $data));
    }

    /**
     * List keys
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/keys", name="metasets_sets_keys")
     */
    public function keysAction(Request $request)
    {
        $id = $request->get('id');

        $repository = $this->get('metasets.repository');
        $set     = $repository->find($id);
        $values  = $set->getKeys();

        $data = array();
        foreach ($values as $row) {
            $data[] = array(
                'key'          => $row['key'],
                'type'         => $row['type'],
                'required'     => $row['required'],
                'synchronized' => $row['synchronized'],
                'readonly'     => $row['readonly'],
                'options'      => $row['options'],
            );
        }

        return new JsonResponse(array('values' => $data));
    }

    /**
     * Create set
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="metasets_sets_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title', 'new_set');

        $set = new MetaSet();
        $set->setTitle($title);

        $repository = $this->get('metasets.repository');
        $repository->save($set);

        return new ResultResponse(true, 'Set "'.$set->getTitle().'" created.');
    }

    /**
     * Save set
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="metasets_sets_save")
     */
    public function saveAction(Request $request)
    {
        $id   = $request->get('id');
        $data = $request->get('data');
        $data = json_decode($data);

        $keys    = array();
        foreach ($data as $item) {
            if (!empty($item['options'])) {
                $options = array();
                foreach (explode(',', $item['options']) as $key => $value) {
                    $options[$key] = trim($value);
                }
                $item['options'] = implode(',', $options);
            }

            $keys[$item['key']] = array(
                'type'         => $item['type'],
                'required'     => !empty($item['required']) ? 1 : 0,
                'synchronized' => !empty($item['synchronized']) ? 1 : 0,
                'readonly'     => !empty($item['readonly']) ? 1 : 0,
                'options'      => !empty($item['options']) ? $item['options'] : null,
            );
        }

        $set = $this->get('metasets.repository')->find($id);
        $set->setKeys($keys)
            ->save();

        return new ResultResponse(true, 'Values for set "'.$set->getTitle().'" saved.');
    }
}
