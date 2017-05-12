<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\MetaSetBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Component\MetaSet\Model\MetaSetInterface;
use Phlexible\Component\Util\UuidUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sets controller.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/metasets/sets")
 * @Security("is_granted('ROLE_META_SETS')")
 */
class SetsController extends Controller
{
    /**
     * List sets.
     *
     * @return JsonResponse
     * @Route("/list", name="metasets_sets_list")
     */
    public function listAction()
    {
        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSet = $metaSetManager->findAll();

        $data = [];
        foreach ($metaSet as $set) {
            /* @var $set MetaSetInterface */

            $data[] = [
                'id' => $set->getId(),
                'name' => $set->getName(),
                'createdAt' => $set->getCreatedAt() ? $set->getCreatedAt()->format('Y-m-d H:i:s') : null,
                'createUser' => $set->getCreatedBy(),
                'modifiedAt' => $set->getModifiedAt() ? $set->getModifiedAt()->format('Y-m-d H:i:s') : null,
                'modifyUser' => $set->getModifiedBy(),
                'revision' => $set->getRevision(),
            ];
        }

        return new JsonResponse(['sets' => $data]);
    }

    /**
     * List fields.
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/fields", name="metasets_sets_fields")
     */
    public function fieldsAction(Request $request)
    {
        $id = $request->get('id');

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSet = $metaSetManager->find($id);
        $fields = $metaSet->getFields();

        $data = [];
        foreach ($fields as $field) {
            $data[] = [
                'id' => $field->getId(),
                'key' => $field->getName(),
                'type' => $field->getType(),
                'required' => $field->isRequired(),
                'synchronized' => $field->isSynchronized(),
                'readonly' => $field->isReadonly(),
                'options' => $field->getOptions(),
            ];
        }

        return new JsonResponse(['values' => $data]);
    }

    /**
     * Create set.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="metasets_sets_create")
     */
    public function createAction(Request $request)
    {
        $name = $request->get('name', 'new_set');

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');

        if ($metaSetManager->findOneBy(array('name' => $name))) {
            return new ResultResponse(false, 'Name already in use.');
        }

        $metaSet = $metaSetManager->createMetaSet();
        $metaSet
            ->setId(UuidUtil::generate())
            ->setName($name)
            ->setCreatedBy($this->getUser()->getDisplayName())
            ->setCreatedAt(new \DateTime())
            ->setModifiedBy($this->getUser()->getDisplayName())
            ->setModifiedAt(new \DateTime());

        $metaSetManager->updateMetaSet($metaSet);

        return new ResultResponse(true, "Meta Set {$metaSet->getName()} created.");
    }

    /**
     * Rename set.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/rename", name="metasets_sets_rename")
     */
    public function renameAction(Request $request)
    {
        $id = $request->get('id');
        $name = $request->get('name');

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');

        if ($metaSetManager->findOneBy(array('name' => $name))) {
            return new ResultResponse(false, 'Name already in use.');
        }

        $metaSet = $metaSetManager->find($id);
        $oldName = $metaSet->getName();

        $metaSet->setName($name);
        $metaSetManager->updateMetaSet($metaSet);

        return new ResultResponse(true, "Meta Set $oldName renamed to $name.");
    }

    /**
     * Save set.
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="metasets_sets_save")
     */
    public function saveAction(Request $request)
    {
        $id = $request->get('id');
        $data = $request->get('data');
        $data = json_decode($data, true);

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $metaSet = $metaSetManager->find($id);

        $metaSet->setRevision($metaSet->getRevision() + 1);

        $fields = [];
        foreach ($metaSet->getFields() as $field) {
            $fields[$field->getId()] = $field;
        }

        foreach ($data as $item) {
            if (!empty($item['options'])) {
                $options = [];
                foreach (explode(',', $item['options']) as $key => $value) {
                    $options[$key] = trim($value);
                }
                $item['options'] = implode(',', $options);
            }

            if (isset($fields[$item['id']])) {
                $field = $fields[$item['id']];
                unset($fields[$item['id']]);
            } else {
                $field = $metaSetManager->createMetaSetField()
                    ->setId(UuidUtil::generate());
            }

            $field
                ->setName($item['key'])
                ->setMetaSet($metaSet)
                ->setType($item['type'])
                ->setRequired(!empty($item['required']) ? 1 : 0)
                ->setSynchronized(!empty($item['synchronized']) ? 1 : 0)
                ->setReadonly(!empty($item['readonly']) ? 1 : 0)
                ->setOptions(!empty($item['options']) ? $item['options'] : null);

            $metaSet->addField($field);
        }

        $metaSet
            ->setModifiedBy($this->getUser()->getDisplayName())
            ->setModifiedAt(new \DateTime());

        foreach ($fields as $field) {
            $metaSet->removeField($field);
        }

        $metaSetManager->updateMetaSet($metaSet);

        return new ResultResponse(true, "Fields saved for set {$metaSet->getName()}.");
    }
}
