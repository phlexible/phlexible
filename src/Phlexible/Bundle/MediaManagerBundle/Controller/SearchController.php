<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\Controller;

use Phlexible\Bundle\MediaSiteBundle\Site\SiteInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Security("is_granted('media')")
 */
class SearchController extends Controller
{
    /**
     * @param string $folderId
     *
     * @return SiteInterface
     */
    private function getSiteByFolderId($folderId)
    {
        $siteManager = $this->get('phlexible_media_site.site_manager');

        return $siteManager->getByFolderId($folderId);
    }

    /**
     * Return files list with filter
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/search", name="mediamanager_file_search")
     */
    public function searchAction(Request $request)
    {
        $searchValues = $request->get('searchValues', '');
        $searchValues = json_decode($searchValues, true);

        if (empty($searchValues)) {
            return new JsonResponse(array());
        }

        // TODO: need site_id
        $site = $this->get('phlexible_media_site.site_manager')->get('mediamanager');

        $db = $this->get('connection_manager')->default;

        $query = $db->select()
            ->distinct()
            ->from(array('fi' => $db->prefix . 'mediamanager_files'), 'fi.id');

        $searchTitle = array();
        $searchSize = array();
        $searchAsset = array();
        $searchAge = array();

        foreach ($searchValues as $key => $value) {
            if ($key == 'basic_term') {
                if (!is_array($value)) {
                    $value = array($value);
                }

                if (!empty($searchValues['basic_in_title'])) {
                    foreach ($value as $title) {
                        if ($title) {
                            $searchTitle[] = 'fi.name LIKE ' . $db->quote('%' . $title . '%');
                        }
                    }
                }

                if (!empty($searchValues['basic_in_meta'])) {
                    $query->joinLeft(
                        array('fm' => $db->prefix . 'mediamanager_files_metasets_items'),
                        'fi.id = fm.file_id',
                        array()
                    );

                    foreach ($value as $title) {
                        if ($title) {
                            $searchTitle[] =
                                '(fm.meta_value_de LIKE ' . $db->quote('%' . $title . '%') . ' OR ' .
                                'fm.meta_value_en LIKE ' . $db->quote('%' . $title . '%') . ')';
                        }
                    }
                }
            }

            if (substr($key, 0, 6) == 'asset_') {
                $searchAsset[] = 'fi.asset_type = ' . $db->quote(strtoupper(substr($key, 6)));
            }

            if ($key == 'size_size') {
                $size = $searchValues['size_size'];
                $value = $searchValues['size_value'];
                $unit = $searchValues['size_unit'];

                if (!is_array($searchValues['size_size'])) {
                    $size = array($size);
                    $value = array($value);
                    $unit = array($unit);
                }

                foreach ($size as $sizeKey => $dummy) {
                    if (!$value[$sizeKey]) {
                        continue;
                    }

                    switch ($unit[$sizeKey]) {
                        case 'g':
                            $value[$sizeKey] = $value[$sizeKey] * 1000 * 1000 * 1000;
                            break;

                        case 'm':
                            $value[$sizeKey] = $value[$sizeKey] * 1000 * 1000;
                            break;

                        case 'k':
                            $value[$sizeKey] = $value[$sizeKey] * 1000;
                            break;

                        case 'b':
                            break;

                        default:
                            continue;
                    }

                    switch ($size[$sizeKey]) {
                        case '<':
                            $searchSize[] = 'fi.size < ' . $db->quote($value[$sizeKey]);
                            break;

                        case '>':
                            $searchSize[] = 'fi.size > ' . $db->quote($value[$sizeKey]);
                            break;

                        default:
                            continue;
                    }
                }

            }

            if ($key == 'date_date') {
                if (empty($searchValues['date_value'])) {
                    continue;
                }

                $date = $searchValues['date_value'];

                switch ($value) {
                    case '<':
                        $query->where('UNIX_TIMESTAMP(fi.create_time) < UNIX_TIMESTAMP(?)', $date);
                        break;

                    case '=':
                        $query->where('DATE_FORMAT(fi.create_time, "%Y-%m-%d") = DATE_FORMAT(?, "%Y-%m-%d")', $date);
                        break;

                    case '>':
                        $query->where('UNIX_TIMESTAMP(fi.create_time) > UNIX_TIMESTAMP(?)', $date);
                        break;

                    default:
                        continue;
                }
            }

            if (substr($key, 0, 4) == 'age_') {
                switch (substr($key, 4)) {
                    case 'last_hour':
                        $searchAge[] = 'UNIX_TIMESTAMP(fi.create_time) > UNIX_TIMESTAMP(NOW()) - 3600';
                        break;

                    case 'last_four_hours':
                        $searchAge[] = 'UNIX_TIMESTAMP(fi.create_time) > UNIX_TIMESTAMP(NOW()) - 14400';
                        break;

                    case 'today':
                        $searchAge[] = 'DATE_FORMAT(fi.create_time, "%Y-%m-%d") = DATE_FORMAT(NOW(), "%Y-%m-%d")';
                        break;

                    case 'yesterday':
                        $searchAge[] = 'DATE_FORMAT(fi.create_time, "%Y-%m-%d") = DATE_FORMAT(NOW() - INTERVAL 1 DAY, "%Y-%m-%d")';
                        break;

                    case 'this_week':
                        $searchAge[] = 'UNIX_TIMESTAMP(fi.create_time) > UNIX_TIMESTAMP(NOW() - INTERVAL 7 DAY)';
                        break;

                    case 'last_week':
                        $searchAge[] = 'UNIX_TIMESTAMP(fi.create_time) > UNIX_TIMESTAMP(NOW() - INTERVAL 14 DAY)';
                        break;

                    case 'this_month':
                        $searchAge[] = 'UNIX_TIMESTAMP(fi.create_time) > UNIX_TIMESTAMP(NOW() - INTERVAL 1 MONTH)';
                        break;

                    case 'last_month':
                        $searchAge[] = 'UNIX_TIMESTAMP(fi.create_time) > UNIX_TIMESTAMP(NOW() - INTERVAL 2 MONTH)';
                        break;
                }
            }

            //            if (!empty($searchValues['below']))
            //            {
            //                $query->where('folder_id IN (SELECT f2.id FROM '.$db->prefix.'mediamanager_folders f1, '.$db->prefix.'mediamanager_folders f2 WHERE f1.id=? AND f2.path LIKE CONCAT(f1.path, "%"))', $searchValues['below']);
            //            }

            if ($key == 'duplicate' && $value) {
                switch ($value) {
                    case 'identical':
                        $query->group('hash')
                            ->having('COUNT(id) > 1')
                            ->limit(20);

                        break;

                    case 'name':
                        $query->group('name')
                            ->having('COUNT(id) > 1')
                            ->limit(20);

                        break;

                    case 'filesize':
                        $query->group('size')
                            ->having('COUNT(id) > 1')
                            ->limit(20);

                        break;
                }
            }
        }

        if (sizeof($searchTitle)) {
            $query->where(implode(' OR ', $searchTitle));
        }

        if (sizeof($searchSize)) {
            $query->where(implode(' AND ', $searchSize));
        }

        if (sizeof($searchAsset)) {
            $query->where(implode(' OR ', $searchAsset));
        }

        if (sizeof($searchAge)) {
            $query->where(implode(' OR ', $searchAge));
        }

        //        echo $query;die;

        $ids = $db->fetchCol($query);

        $files = array();
        foreach ($ids as $id) {
            $file = $site->getFilePeer()->getByID($id);

            if (!$file->getFolder()->checkRight(MWF_Env::getUser(), 'FILE_READ')) {
                continue;
            }

            $files[] = $file;
        }

        $data = array();
        if (count($files)) {
            $data = $this->filesToArray($files);
        }

        return new JsonResponse(array('files' => $data));
    }
}
