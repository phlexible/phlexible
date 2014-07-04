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

        $file = $this->get('phlexible_media_site.manager')->getByFileId($fileId)->findFile($fileId, $fileVersion);

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $fileMetaDataManager = $this->get('phlexible_media_manager.file_meta_data_manager');

        $meta = array();
        foreach ($file->getAttribute('metasets', array()) as $metaSetId) {
            $metaSet = $metaSetManager->find($metaSetId);
            $metaData = $fileMetaDataManager->findByMetaSetAndFile($metaSet, $file);

            if (!$metaData) {
                continue;
            }

            $fieldDatas = array();
            foreach ($metaSet->getFields() as $field) {
                $fieldData = array(
                    'key'          => $field->getName(),
                    'type'         => $field->getType(),
                    'options'      => $field->getOptions(),
                    'readonly'     => $field->isReadonly(),
                    'required'     => $field->isRequired(),
                    'synchronized' => $field->isSynchronized(),
                );
                foreach ($metaData->getLanguages() as $language) {
                    $fieldData["value_$language"] = $metaData->get($field->getName(), $language);
                }
                $fieldDatas[] = $fieldData;
            }

            $meta[] = array(
                'set_id' => $metaSetId,
                'title'  => $metaSet->getName(),
                'fields' => $fieldDatas
            );
        }

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

        $siteManager = $this->get('phlexible_media_site.manager');
        $fileMetaSetResolver = $this->get('phlexible_media_manager.file_meta_set_resolver');

        $folder = $siteManager->getByFileId($fileId)->findFile($fileId, $fileVersion);
        $metaSets = $fileMetaSetResolver->resolve($folder);

        $sets = array();
        foreach ($metaSets as $metaSet) {
            $sets[] = array(
                'set_id' => $metaSet->getId(),
                'name'   => $metaSet->getName(),
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

        $metaSetManager = $this->get('phlexible_meta_set.meta_set_manager');
        $fileMetaSetResolver = $this->get('phlexible_media_manager.file_meta_set_resolver');
        $siteManager = $this->get('phlexible_media_site.manager');

        $file = $siteManager->getByFileId($fileId)->findFile($fileId, $fileVersion);

        $metaSets = $metaSetManager->findAll();
        foreach ($fileMetaSetResolver->resolve($file) as $index => $metaSet) {
            if (in_array($metaSet, $metaSets)) {
                unset($metaSets[$index]);
            }
        }

        $sets = array();
        foreach ($metaSets as $metaSet) {
            $sets[] = array(
                'set_id' => $metaSet->getId(),
                'name'   => $metaSet->getTitle(),
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

        $siteManager = $this->get('phlexible_media_site.manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);

        $attributes = $file->getAttributes();
        if (!isset($attributes['metasets'])) {
            $attributes['metasets'] = array();
        }
        if (!in_array($setId, $attributes['metasets'])) {
            $attributes['metasets'][] = $setId;
            $site->setFileAttributes($file, $attributes);
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

        $siteManager = $this->get('phlexible_media_site.manager');

        $site = $siteManager->getByFileId($fileId);
        $file = $site->findFile($fileId, $fileVersion);

        $attributes = $file->getAttributes();
        if (isset($attributes['metasets']) && in_array($setId, $attributes['metasets'])) {
            unset($attributes['metasets'][array_search($setId, $attributes['metasets'])]);
            $site->setFileAttributes($file, $attributes);
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

        $site = $this->getContainer()->get('phlexible_media_site.manager')->getByFileId($fileId);
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
