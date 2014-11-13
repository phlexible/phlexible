<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\Controller;

use Phlexible\Component\Formatter\FilesizeFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Items controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ItemsController extends Controller
{
    /**
     * Deliver a media asset
     */
    public function indexAction()
    {
        $eid = $this->_getParam('eid');
        $version = $this->_getParam('version');
        $language = $this->_getParam('language');
        $dataId = $this->_getParam('data_id');
        $fileId = $this->_getParam('file_id');
        $fileVersion = $this->_getParam('file_version', 1);

        if (!$fileVersion) {
            $fileVersion = 1;
        }

        try {
            $templateData = [];

            $db = $this->getContainer()->dbPool->read;

            $select = $db->select()
                ->from(['ed' => $db->prefix . 'element_data'], [])
                ->join(
                    ['ev' => $db->prefix . 'element_version'],
                    'ev.eid = ed.eid AND ev.version = ed.version',
                    []
                )
                ->join(
                    ['etv' => $db->prefix . 'elementtype_version'],
                    'etv.element_type_id = ev.element_type_id AND etv.version = ev.element_type_version',
                    []
                )
                ->join(
                    ['ets' => $db->prefix . 'elementtype_structure'],
                    'ets.element_type_id = etv.element_type_id AND ets.version = etv.version AND ed.ds_id = ets.ds_id',
                    ['media']
                )
                ->where('ed.eid = ?', $eid)
                ->where('ed.version = ?', $version)
                ->where('ed.data_id = ?', $dataId);

            $media = $db->fetchOne($select);

            if (empty($media)) {
                $select = $db->select()
                    ->from(['ed' => $db->prefix . 'element_data'], [])
                    ->join(
                        ['ev' => $db->prefix . 'element_version'],
                        'ev.eid = ed.eid AND ev.version = ed.version',
                        []
                    )
                    ->join(
                        ['etv' => $db->prefix . 'elementtype_version'],
                        'etv.element_type_id = ev.element_type_id AND etv.version = ev.element_type_version',
                        []
                    )
                    ->join(
                        ['ets' => $db->prefix . 'elementtype_structure'],
                        'ets.element_type_id = etv.element_type_id AND ets.version = etv.version',
                        []
                    )
                    ->join(
                        ['etsr' => $db->prefix . 'elementtype_structure'],
                        'etsr.element_type_id = ets.reference_id AND etsr.version = ets.reference_version AND ed.ds_id = etsr.ds_id',
                        ['media']
                    )
                    ->where('ed.eid = ?', $eid)
                    ->where('ed.version = ?', $version)
                    ->where('ed.data_id = ?', $dataId);

                $media = $db->fetchOne($select);
            }

            if (!empty($media)) {
                $media = unserialize($media);
                foreach ($media['imageList'] as $templateItem) {
                    $templateData[$templateItem[0]] = [];
                }

                //echo "<pre>"; print_r($item);die;

                $cacheManager = Media_Cache_Manager::getInstance();

                foreach ($templateData as $templateKey => $templateRow) {
                    $template = $this->getContainer()->get('mediatemplates.repository')->find($templateKey);

                    $templateData[$templateKey]['key'] = $templateKey;
                    $templateData[$templateKey]['type'] = $template->getType();

                    $cacheItem = $cacheManager->getByTemplateAndFile($templateKey, $fileId, $fileVersion);

                    $formatter = new FilesizeFormatter();

                    $templateData[$templateKey]['cache_id'] = $cacheItem->getId();
                    $templateData[$templateKey]['width'] = $cacheItem->getWidth();
                    $templateData[$templateKey]['height'] = $cacheItem->getHeight();
                    $templateData[$templateKey]['size'] = $cacheItem->getFileSize();
                    $templateData[$templateKey]['readablesize'] = $formatter->formatFilesize($cacheItem->getFileSize());
                    $templateData[$templateKey]['mimetype'] = $cacheItem->getMimeType();

                    switch ($cacheItem->getStatus()) {
                        case Media_Cache_Item::STATUS_WAITING:
                            $status = 'waiting';
                            break;

                        case Media_Cache_Item::STATUS_OK:
                            $status = 'ok';
                            break;

                        case Media_Cache_Item::STATUS_DELEGATE:
                            $status = 'delegate';
                            break;

                        case Media_Cache_Item::STATUS_MISSING:
                            $status = 'missing';
                            break;

                        case Media_Cache_Item::STATUS_ERROR:
                            $status = 'error';
                            break;

                        default:
                            $status = 'unknown';
                            break;
                    }
                    $templateData[$templateKey]['status'] = $status;
                }

                $templateData = array_values($templateData);
            }
        } catch (\Exception $e) {
            MWF_Log::exception($e);
            $templateData = [];
        }

        $this->_response->setAjaxPayload(['templates' => $templateData]);
    }
}
