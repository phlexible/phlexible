<?php
/**
 * MAKEweb
 *
 * PHP Version 5
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 * @version     SVN: $Id: Generator.php 2312 2007-01-25 18:46:27Z swentz $
 */

use Phlexible\CoreComponent\Event\FlushEvent;

/**
 * Elements callbacks
 *
 * @category    MAKEweb
 * @package     Makeweb_Elements
 * @author      Stephan Wentz <sw@brainbits.net>
 * @copyright   2007 brainbits GmbH (http://www.brainbits.net)
 */
class Makeweb_Elements_Callback
{
    protected static function _doTask($node, $language, $neededTask)
    {
        if (!class_exists('Makeweb_Tasks_Task_Manager')) {
            return;
        }

        $manager = Makeweb_Tasks_Task_Manager::getInstance();

        try {
            $task = $manager->getByPayload(array('tid' => $node->getId()), $neededTask);
        } catch (Exception $e) {
            $task = null;
        }

        /* @var $task Makeweb_Tasks_Task_Abstract */

        if ($task && get_class($task) === $neededTask) {
            $payload = $task->getPayload();

            if (!empty($payload['language']) && $payload['language'] != $language) {
                return;
            }

            if (!in_array(
                $task->getLatestStatus()->getStatus(),
                array(
                    Makeweb_Tasks_Task_Abstract::STATUS_OPEN,
                    Makeweb_Tasks_Task_Abstract::STATUS_REOPENED,
                )
            )
            ) {
                return;
            }

            $t9n = MWF_Registry::getContainer()->t9n;

            $task->createStatus($t9n->elements->task_done, Makeweb_Tasks_Task_Abstract::STATUS_FINISHED);
        }
    }

    protected static function _removeMatchingTask($node)
    {
        if (!class_exists('Makeweb_Tasks_Task_Manager')) {
            return;
        }

        $manager = Makeweb_Tasks_Task_Manager::getInstance();

        try {
            $onlyStatus = array(
                Makeweb_Tasks_Task_Abstract::STATUS_OPEN,
                Makeweb_Tasks_Task_Abstract::STATUS_REJECTED,
                Makeweb_Tasks_Task_Abstract::STATUS_REOPENED,
                Makeweb_Tasks_Task_Abstract::STATUS_FINISHED,
            );

            $task = $manager->getByPayload(array('tid' => $node->getId()), null, $onlyStatus);
        } catch (Exception $e) {
            return;
        }

        /* @var $task Makeweb_Tasks_Task_Abstract */

        if ($task) {
            $payload = $task->getPayload();

            $t9n = MWF_Registry::getContainer()->t9n;

            $task->createStatus($t9n->elements->task_delete, Makeweb_Tasks_Task_Abstract::STATUS_CLOSED);
        }
    }

    /*
    protected static function _queueDataSourceCleanupForSuggestFields(Makeweb_Elements_Tree_Node $node,
                                                                      $language)
    {
        // fetch online version
        $elementVersionManager = Makeweb_Elements_Element_Version_Manager::getInstance();
        $elementVersion = $elementVersionManager->get(
            $node->getEid(),
            $node->getOnlineVersion($language)
        );

        // fetch element type structure
        $elementtypeStructureManager =
            Makeweb_Elementtypes_Elementtype_Structure_Manager::getInstance();

        $structureTree = $elementtypeStructureManager->getTree(
            $elementVersion->getElementTypeID(),
            $elementVersion->getElementTypeVersion()
        );

        // fetch ds_ids of suggest fields
        $suggestFieldDsIds = $structureTree->getDsIdsByFieldType('suggest');

        if (count($suggestFieldDsIds))
        {
            // add cleanup job for suggets fields
            $queueManager = MWF_Core_Queue_Manager::getInstance();
            foreach ($suggestFieldDsIds as $dsId)
            {
                $structureNode = $structureTree->getByDsId($dsId);
                $sourceId = $structureNode->getOptionsValue('source_source', false);

                if ($sourceId)
                {
                    $job = new MWF_Core_DataSources_Queue_Cleanup($sourceId);
                    $queueManager->addJob($job, MWF_Core_Queue_Manager::PRIORITY_LOW);
                }
            }
        }
    }
    */

}
