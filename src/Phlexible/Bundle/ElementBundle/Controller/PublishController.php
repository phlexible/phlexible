<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Publish controller
 *
 * @author Stephan Wentz <sw@brainbits.net>
 * @Route("/elements/publish")
 * @Security("is_granted('elements')")
 */
class PublishController extends Controller
{
    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("", name="elements_publish")
     */
    public function publishAction(Request $request)
    {
        $tid      = $request->get('tid');
        $teaserId = $request->get('teaser_id', null);
        $version  = $request->get('version', null);
        $language = $request->get('language');
        $comment  = $request->get('comment', '');

        //$fileUsage = new Makeweb_Elements_Element_FileUsage(MWF_Registry::getContainer()->dbPool);

        if ($teaserId === null) {
            $treeManager = Makeweb_Elements_Tree_Manager::getInstance();
            $node = $treeManager->getNodeByNodeId($tid);
            $tree = $node->getTree();

            $tree->publishNode($node, $language, $version, false, $comment);

            $eid = $node->getEid();
            //$fileUsage->update($node->getEid());

            $data = array();

            $elementVersionManager = Makeweb_Elements_Element_Version_Manager::getInstance();
            $elementVersion = $elementVersionManager->get($node->getEid(), $version);

            $iconStatus   = $node->isAsync($language) ? 'async' : ($node->isPublished($language) ? 'online' : null);
            $iconInstance = ($node->isInstance() ? ($node->isInstanceMaster() ? 'master' : 'slave') : false);

            $data['icon'] = $elementVersion->getIconUrl($node->getIconParams($language));

            $response = new ResultResponse(true, 'TID "'.$tid.'" published.', $data);
        } else {
            $teasersManager = Makeweb_Teasers_Manager::getInstance();

            $eid = $teasersManager->publish($teaserId, $version, $language, $comment, $tid);

            //$fileUsage->update($eid);

            $response = new ResultResponse(true, 'Teaser ID "'.$teaserId.'" published.');
        }

        $queueService = $this->getContainer()->get('queue.service');
        $job = new Makeweb_Elements_Job_UpdateUsage();
        $job->setEid($eid);
        $queueService->addUniqueJob($job);

        // workaround to fix missing catch results for non master language elements
        Makeweb_Elements_Element_History::insert(
            Makeweb_Elements_Element_History::ACTION_SAVE,
            $eid,
            $version,
            $language
        );

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @Route("/preview", name="elements_publish_preview")
     */
    public function previewpublishAction(Request $request)
    {
        $tid                     = $request->get('tid');
        $teaserId                = $request->get('teaser_id', null);
        $language                = $request->get('language');
        $languages               = $request->get('languages');
        $version                 = $request->get('version', null);
        $includeElements         = (bool) $request->get('include_elements', false);
        $includeElementInstances = (bool) $request->get('include_element_instances', false);
        $includeTeasers          = (bool) $request->get('include_teasers', false);
        $includeTeaserInstances  = (bool) $request->get('include_teaser_instances', false);
        $recursive               = (bool) $request->get('recursive', false);
        $onlyOffline             = (bool) $request->get('only_offline', false);
        $onlyAsync               = (bool) $request->get('only_async', false);

        if ($languages) {
            $languages = explode(',', $languages);
        } else {
            $languages = array($language);
        }

        $treeManager = Makeweb_Elements_Tree_Manager::getInstance();
        $elementVersionManager = Makeweb_Elements_Element_Version_Manager::getInstance();
        $db = $this->getContainer()->dbPool->default;
        $contentRightsManager = $this->getContainer()->contentRightsManager;
        $currentUser = MWF_Env::getUser();

        $result = array();

        $publish = new Makeweb_Elements_Publish($db, $treeManager, $elementVersionManager, $contentRightsManager, $currentUser);

        foreach ($languages as $language) {
            $langResult = $publish->getPreview($tid, $teaserId, $language, $version, $includeElements, $includeElementInstances, $includeTeasers, $includeTeaserInstances, $recursive, $onlyOffline, $onlyAsync);
            $result = array_merge($result, $langResult);
        }

        foreach ($result as $key => $row) {
            $result[$key]['action'] = true;
        }

        return new JsonResponse(array('preview' => $result));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @throws Makeweb_Elements_Element_Exception
     * @Route("/advanced", name="elements_publish_advanced")
     */
    public function advancedpublishAction(Request $request)
    {
        $tid      = $request->get('tid');
        $version  = $request->get('version');
        $language = $request->get('language');
        $comment  = $request->get('comment');
        $data     = $request->get('data');
        $data     = json_decode($data, true);

        $lock = new \Brainbits_Util_FileLock($this->getContainer()->getParameter('app.lock_dir') . 'elements_publish_lock');
        if (!$lock->tryLock()) {
            throw new Makeweb_Elements_Element_Exception('Another advanced publish running.');
        }

        $treeManager = Makeweb_Elements_Tree_Manager::getInstance();
        $db = $this->getContainer()->dbPool->default;

        //$fileUsage = new Makeweb_Elements_Element_FileUsage(MWF_Registry::getContainer()->dbPool);

        $queueService = $this->getContainer()->get('queue.service');
        $db->beginTransaction();

        foreach ($data as $row) {
            set_time_limit(15);
            if (empty($row['teaser_id'])) {
                $node = $treeManager->getNodeByNodeId($row['tid']);
                $tree = $node->getTree();

                $tree->publishNode($node, $row['language'], $row['version'], false, $comment);

                $eid = $node->getEid();
                //$fileUsage->update($node->getEid());
            } else {
                $teaserManager = Makeweb_Teasers_Manager::getInstance();

                $eid = $teaserManager->publish($row['teaser_id'], $row['version'], $row['language'], $comment, $row['tid']);

                //$fileUsage->update($eid);
            }

            $job = new Makeweb_Elements_Job_UpdateUsage();
            $job->setEid($eid);
            $queueService->addUniqueJob($job);
        }

        $db->commit();

        $data = array();

        $node = $treeManager->getNodeByNodeId($tid);
        $elementVersionManager = Makeweb_Elements_Element_Version_Manager::getInstance();
        $elementVersion = $elementVersionManager->get($node->getEid(), $version);

        $iconStatus   = $node->isAsync($language) ? 'async' : ($node->isPublished($language) ? 'online' : null);
        $iconInstance = ($node->isInstance() ? ($node->isInstanceMaster() ? 'master' : 'slave') : false);

        $data['icon'] = $elementVersion->getIconUrl($node->getIconParams($language));

        $lock->unlock();

        return new ResultResponse(true, 'Successfully published.', $data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/setoffline", name="elements_publish_setoffline")
     */
    public function setofflineAction(Request $request)
    {
        $tid      = $request->get('tid');
        $teaserId = $request->get('teaser_id', null);
        $language = $request->get('language');
        $comment  = $request->get('comment', '');

        //$fileUsage = new Makeweb_Elements_Element_FileUsage(MWF_Registry::getContainer()->dbPool);

        if ($teaserId === null) {
            $manager = Makeweb_Elements_Tree_Manager::getInstance();
            $node = $manager->getNodeByNodeId($tid);
            $tree = $node->getTree();

            $tree->setNodeOffline($node, $language, false, $comment);

            $eid = $node->getEid();
            //$fileUsage->update($node->getEid());

            $response = new ResultResponse(true, 'TID "'.$tid.'" set offline.');
        } else {
            $db = $this->getContainer()->dbPool->default;

            $select = $db->select()
                ->from($db->prefix . 'element_tree_teasers', 'teaser_eid')
                ->where('id = ?', $teaserId)
                ->limit(1);

            $eid = $db->fetchOne($select);

            $db->delete(
                $db->prefix . 'element_tree_teasers_online',
                'teaser_id = '.$db->quote($teaserId) . ' AND language = '.$db->quote($language)
            );

            //$fileUsage->update($eid);

            Makeweb_Teasers_History::insert(
                Makeweb_Teasers_History::ACTION_SET_OFFLINE, $teaserId, $eid, null, $language, $comment
            );

            $response = new ResultResponse(true, 'Teaser ID "'.$teaserId.'" set offline.');
        }

        // TODO refactor
        Brainbits_Event_Dispatcher::getInstance()->post(
            (object) array(
                'teaser_id' => $teaserId,
                'language'  => $language,
                'tid'       => $tid,
            ),
            'teaser_setoffline'
        );

        $queueManager = MWF_Core_Queue_Manager::getInstance();
        $job = new Makeweb_Elements_Job_UpdateUsage();
        $job->setEid($eid);
        $queueManager->addUniqueJob($job);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/setoffline/recursive", name="elements_publish_setoffline_recursive")
     */
    public function setofflinerecursiveAction(Request $request)
    {
        $tid      = $request->get('tid');
        $teaserId = $request->get('teaser_id', null);
        $language = $request->get('language');
        $comment  = $request->get('comment', '');

        if ($teaserId === null) {
            $manager = Makeweb_Elements_Tree_Manager::getInstance();
            $node = $manager->getNodeByNodeId($tid);
            $tree = $node->getTree();

            $tree->setNodeOffline($node, $language, true, $comment);
        } else {

        }

        return new ResultResponse(true, 'TID "'.$tid.'" set offline recursively.');
    }
}
