<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * File meta controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/mediamanager/filemeta")
 * @Security("is_granted('media')")
 */
class FileMetaController extends Controller
{
    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("", name="mediamanager_file_meta")
     */
    public function metaAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);

        $file = $this->get('mediasite.manager')->getByFileId($fileId)->findFile($fileId, $fileVersion);

        //$metaResolver = $this->get('mediamanager.meta.resolver');
        $metasetRepository = $this->get('metasets.repository');
        $metadataRepository = $this->get('metasets.data.repository');

        $meta = array();
        foreach ($file->getAttribute('metasets') as $id) {
            $metaset = $metasetRepository->find($id);
            $metadata = $metadataRepository->load(
                $metaset,
                array('file_id' => $fileId, 'file_version' => $fileVersion)
            );

            $fieldDatas = array();
            foreach ($metaset->getFields() as $field) {
                $fieldData = array(
                    'key'          => $field->getKey(),
                    'type'         => $field->getType(),
                    'options'      => $field->getOptions(),
                    'readonly'     => $field->isReadonly(),
                    'required'     => $field->isRequired(),
                    'synchronized' => $field->isSynchronized(),
                    'value_de'     => $metadata->get($field->getKey(), 'de'),
                    'value_en'     => $metadata->get($field->getKey(), 'en'),
                );
                $fieldDatas[] = $fieldData;
            }

            $meta[] = array(
                'set_id' => $id,
                'title'  => $metaset->getTitle(),
                'fields' => $fieldDatas
            );
        }

        //$meta = array();//$metaResolver->getFileMeta($fileId, $fileVersion);

        return new JsonResponse(array('meta' => $meta));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/listsets", name="mediamanager_file_meta_set_list")
     */
    public function listsetsAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);

        $site = $this->get('mediasite.manager')->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);

        $metasetIds = $file->getAttribute('metasets');

        $sets = array();
        foreach ($metasetIds as $metasetId) {
            $sets[] = array(
                'set_id' => $metasetId,
                'name'   => $metasetId
            );
        }

        return new JsonResponse(array('sets' => $sets));
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/availablesets", name="mediamanager_file_meta_set_available")
     */
    public function availablesetsAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);

        $site = $this->get('mediasite.manager')->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);

        $metasetsRepository = $this->get('metasets.repository');

        $metasets = $file->getAttribute('metasets');

        $availableSets = array();
        foreach ($metasetsRepository->findAll() as $metaset) {
            if (!isset($metasets[$metaset->getId()])) {
                $availableSets[] = $metaset;
            }
        }

        $sets = array();
        foreach ($availableSets as $metaset) {
            $sets[] = array(
                'set_id' => $metaset->getId(),
                'name'   => $metaset->getTitle()
            );
        }

        return new JsonResponse(array('sets' => $sets));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/addset", name="mediamanager_file_meta_set_add")
     */
    public function addsetAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);
        $setId = $request->get('set_id');

        $site = $this->get('mediasite.manager')->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);

        $metasets = $file->getAttribute('metasets', array());
        if (!in_array($setId, $metasets)) {
            $metasets[] = $setId;
            $file->setAttribute('metasets', $metasets);
            $site->setFileAttributes($file, $file->getAttributes());
        }

        return new ResultResponse(true, 'Set added.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/removeset", name="mediamanager_file_meta_set_remove")
     */
    public function removesetAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);
        $setId = $request->get('set_id');

        $site = $this->get('mediasite.manager')->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);


        $metasets = $file->getAttribute('metasets', array());
        if (in_array($setId, $metasets)) {
            unset($metasets[array_search($setId, $metasets)]);
            $file->setAttribute('metasets', $metasets);
            $site->setFileAttributes($file, $file->getAttributes());
        }

        return new ResultResponse(true, 'Set removed.');
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/save", name="mediamanager_file_meta_save")
     */
    public function savemetaAction(Request $request)
    {
        $fileId = $request->get('file_id');
        $fileVersion = $request->get('file_version', 1);
        $data = $request->get('data');
        $data = json_decode($data);

        $dispatcher = $container->dispatcher;

        $metaDefaultLanguage = $registry->getValue('system.languages.language.meta');
        $metaLanguages = $languagesManager->getSet('meta');

        $site = $this->getContainer()->get('mediasite.manager')->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);
        $asset = $this->getContainer()->mediaAssetManager->find($file);

        $event = new BeforeSaveMetaEvent($file);
        if ($dispatcher->dispatch($event) === false) {
            $this->_response->setResult(false, null, $event->getCancelReason());

            return;
        }

        $metaSetItems = $asset->getMetaSetItems($metaDefaultLanguage);

        foreach ($data as $key => $row) {
            if ('suggest' === $metaSetItems[$row['set_id']]->getType($key)) {
                $dataSourceId = $metaSetItems[$row['set_id']]->getOptions($key);
                $dataSourcesRepository = $container->dataSourcesRepository;
                $dataSource = $dataSourcesRepository->getDataSourceById(
                    $dataSourceId,
                    $metaDefaultLanguage
                );
                $dataSourceKeys = $dataSource->getKeys();
                $dataSourceModified = false;
                foreach (explode(',', $row['value_' . $metaDefaultLanguage]) as $singleValue) {
                    if (!in_array($singleValue, $dataSourceKeys)) {
                        $dataSource->addKey($singleValue, true);
                        $dataSourceModified = true;
                    }
                }
                if ($dataSourceModified) {
                    $dataSourcesRepository->save($dataSource, $this->getUser()->getId());
                }
            }

            $metaSetItems[$row['set_id']]->$key = $row['value_' . $metaDefaultLanguage];
        }

        foreach ($metaSetItems as $metaSetItem) {
            $metaSetItem->save();
        }

        unset($metaSetItems);

        foreach ($metaLanguages as $metaLanguage) {
            if ($metaLanguage === $metaDefaultLanguage) {
                continue;
            }

            $metaSetItems = $asset->getMetaSetItems($metaLanguage);

            foreach ($data as $key => $row) {
                if ('suggest' === $metaSetItems[$row['set_id']]->getType($key)) {
                    $dataSourceId = $metaSetItems[$row['set_id']]->getOptions($key);
                    $dataSourcesRepository = $container->dataSourcesRepository;
                    $dataSource = $dataSourcesRepository->getDataSourceById(
                        $dataSourceId,
                        $metaLanguage
                    );
                    $dataSourceKeys = $dataSource->getKeys();
                    $dataSourceModified = false;
                    foreach (explode(',', $row['value_' . $metaLanguage]) as $singleValue) {
                        if (!in_array($singleValue, $dataSourceKeys)) {
                            $dataSource->addKey($singleValue, true);
                            $dataSourceModified = true;
                        }
                    }
                    if ($dataSourceModified) {
                        $dataSourcesRepository->save($dataSource, $this->getUser()->getId());
                    }
                }

                $metaSetItems[$row['set_id']]->$key = $row['value_' . $metaLanguage];
            }

            foreach ($metaSetItems as $metaSetItem) {
                $metaSetItem->save();
            }
        }

        $event = new SaveMetaEvent($file);
        $dispatcher->dispatch($event);

        $db = $this->getContainer()->dbPool->default;
        $updateData = array(
            'modify_user_id' => $this->getUser()->getId(),
            'modify_time'    => $db->fn->now(),
        );
        $where = array(
            'id = ?'      => $file->getId(),
            'version = ?' => $file->getVersion(),
        );
        $db->update(
            $db->prefix . 'mediamanager_files',
            $updateData,
            $where
        );

        return new ResultResponse(true, 'Meta saved.');
    }
}
