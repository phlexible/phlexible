<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\EventListener;

use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeVersionEvent;

/**
 * Elementtype listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeListener
{
    public function onElementtypeVersionCreate(ElementtypeVersionEvent $event)
    {
        return;
        $container = $params['container'];

        try
        {
            $db = $container->dbPool->default;
            $newElementTypeVersion = $event->getNewElementTypeVersion();
            $oldElementTypeVersion = $event->getOldElementTypeVersion();

            if ($oldElementTypeVersion->getID() !== $newElementTypeVersion->getID())
            {
                return;
            }

            $select = $db->select()
                         ->distinct()
                         ->from($db->prefix . 'element_version', 'eid')
                         ->where('element_type_id = ?', $oldElementTypeVersion->getID());

            $eids = $db->fetchCol($select);

            $elementManager = $container->elementsManager;

            foreach ($eids as $eid)
            {
                set_time_limit(45);

                $element = $elementManager->getByEID($eid);
                $oldElementVersion = $element->getLatestVersion();

                $newElementVersion = $oldElementVersion->copy(
                                                       null,
                                                           $newElementTypeVersion->getVersion()
                );

                $select = $db->select()
                             ->from($db->prefix . 'element_tree_page')
                             ->where('eid = ?', $eid)
                             ->where('version = ?', $oldElementVersion->getVersion());

                $pages = $db->fetchAll($select);

                foreach ($pages as $row)
                {
                    $row['version'] = $newElementVersion->getVersion();

                    $db->insert($db->prefix . 'element_tree_page', $row);
                }

                // update element version titles
                $languages = $container->getParam(':frontend.languages.available');
                foreach ($languages as $language)
                {
                    $title = $newElementVersion->getBackendTitle($language);
                }
            }
        }
        catch (\Exception $e)
        {
            echo $select.PHP_EOL;
            echo $oldElementVersion->getVersion().PHP_EOL;
            echo $newElementVersion->getVersion().PHP_EOL;
            print_r($row);
            echo $e->getMessage().PHP_EOL.$e->getTraceAsString();
            die;
        }
    }

    public function onElementtypeDelete(Makeweb_Elementtypes_Event_Delete $event)
    {
        return;
        /* @var $container MWF_Container_ContainerInterface */
        $container       = $params['container'];
        $siterootManager = $container->siterootManager;
        $treeManager     = $container->get('phlexible_tree.manager');

        $siteroots = $siterootManager->getAllSiteRoots();

        foreach ($siteroots as $siterootId => $siteroot)
        {
            try
            {
                $tree = $treeManager->getBySiteRootId($siterootId, true);
            }
            catch (Exception $e)
            {
                MWF_Log::exception($e);
            }
        }
    }
}
