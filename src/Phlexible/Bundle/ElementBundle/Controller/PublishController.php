<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Controller;
use Phlexible\Bundle\ElementBundle\Element\Publish\Selection;
use Phlexible\Bundle\TeaserBundle\Entity\Teaser;
use Phlexible\Bundle\TreeBundle\Model\TreeNodeInterface;
use Phlexible\Component\Util\FileLock;
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

            $response = new ResultResponse(true, "Teaser ID $teaserId published.");
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
    public function previewAction(Request $request)
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

        $selector = $this->get('phlexible_element.publish.selector');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        $selection = new Selection();
        foreach ($languages as $language) {
            $langSelection = $selector->select(
                $tid,
                $language,
                $version,
                $includeElements,
                $includeElementInstances,
                $includeTeasers,
                $includeTeaserInstances,
                $recursive,
                $onlyOffline,
                $onlyAsync
            );
            $selection->merge($langSelection);
        }

        $result = array();
        foreach ($selection->all() as $selectionItem) {
            if ($selectionItem->getTarget() instanceof TreeNodeInterface) {
                $id = $selectionItem->getTarget()->getId();
                $icon = $iconResolver->resolveTreeNode($selectionItem->getTarget(), $selectionItem->getLanguage());
            } else {
                $id = $selectionItem->getTarget()->getId();
                $icon = $iconResolver->resolveTeaser($selectionItem->getTarget(), $selectionItem->getLanguage());
            }

            $result[] = array(
                'type'      => $selectionItem->getTarget() instanceof TreeNodeInterface ? 'full_element' : 'part_element',
                'instance'  => $selectionItem->isInstance(),
                'depth'     => $selectionItem->getDepth(),
                'path'      => $selectionItem->getPath(),
                'id'        => $id,
                'eid'       => $selectionItem->getTarget()->getTypeId(),
                'version'   => $selectionItem->getVersion(),
                'language'  => $selectionItem->getLanguage(),
                'title'     => $selectionItem->getTitle(),
                'icon'      => $icon,
                'action'    => true,
            );
        }

        return new JsonResponse(array('preview' => $result));
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @throws \Exception
     * @Route("/advanced", name="elements_publish_advanced")
     */
    public function advancedPublishAction(Request $request)
    {
        $tid      = $request->get('tid');
        $version  = $request->get('version');
        $language = $request->get('language');
        $comment  = $request->get('comment');
        $data     = $request->get('data');
        $data     = json_decode($data, true);

        $lock = new FileLock($this->container->getParameter('app.lock_dir') . 'elements_publish_lock');
        if (!$lock->acquire()) {
            throw new \Exception('Another advanced publish running.');
        }

        $treeManager = $this->get('phlexible_tree.tree_manager');
        $teaserManager = $this->get('phlexible_teaser.teaser_manager');
        $iconResolver = $this->get('phlexible_element.icon_resolver');

        foreach ($data as $row) {
            set_time_limit(15);
            if ($row['type'] === 'full_element') {
                $tree = $treeManager->getByNodeId($row['id']);
                $treeNode = $tree->get($row['id']);

                $tree->publish($treeNode, $row['version'], $row['language'], $this->getUser()->getId(), $comment);
            } elseif ($row['type'] === 'part_element') {
                $teaser = $teaserManager->find($row['id']);

                $teaserManager->publishTeaser($teaser, $row['version'], $row['language'], $this->getUser()->getId(), $comment);
            } else {
                continue;
            }

            // TODO: update usage
            /*
            $job = new Makeweb_Elements_Job_UpdateUsage();
            $job->setEid($eid);
            $queueService->addUniqueJob($job);
            */
        }

        $data = array();

        $tree = $treeManager->getByNodeId($tid);
        $treeNode = $tree->get($tid);

        $data = array(
            'tid' => $tid,
            'language' => $language,
            'icon' => $iconResolver->resolveTreeNode($treeNode, $language),
        );

        $lock->release();

        return new ResultResponse(true, 'Successfully published.', $data);
    }

    /**
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/setoffline", name="elements_publish_setoffline")
     */
    public function setOfflineAction(Request $request)
    {
        $tid      = $request->get('tid');
        $teaserId = $request->get('teaser_id', null);
        $language = $request->get('language');
        $comment  = $request->get('comment', null);

        //$fileUsage = new Makeweb_Elements_Element_FileUsage(MWF_Registry::getContainer()->dbPool);

        if (!$teaserId) {
            $treeManager = $this->get('phlexible_tree.tree_manager');
            $tree = $treeManager->getByNodeId($tid);
            $node = $tree->get($tid);

            $tree->setOffline($node, $language, $this->getUser()->getId(), $comment);

            //$eid = $node->getEid();
            //$fileUsage->update($node->getEid());

            $response = new ResultResponse(true, "TID $tid set offline.");
        } else {
            $teaserManager = $this->get('phlexible_teaser.teaser_manager');

            $teaser = $teaserManager->find($teaserId);
            $teaserManager->setTeaserOffline($teaser, $language, $this->getUser()->getId(), $comment);

            //$fileUsage->update($eid);

            /*
            Makeweb_Teasers_History::insert(
                Makeweb_Teasers_History::ACTION_SET_OFFLINE, $teaserId, $eid, null, $language, $comment
            );
            */

            $response = new ResultResponse(true, "Teaser ID $teaserId set offline.");
        }

        /*
        $queueManager = MWF_Core_Queue_Manager::getInstance();
        $job = new Makeweb_Elements_Job_UpdateUsage();
        $job->setEid($eid);
        $queueManager->addUniqueJob($job);
        */

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
