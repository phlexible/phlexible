<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TreeBundle\EventListener;

use Phlexible\Bundle\SiterootBundle\Event\CreateSiterootEvent;

/**
 * Create siteroot listener
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CreateSiterootListener
{
    /**
     * @param CreateSiterootEvent $event
     */
    public function onCreateSiteroot(CreateSiterootEvent $event)
    {
        $siteroot = $event->getSiteroot();
        $title    = $siteroot->getTitle();
        $siterootID = $siteroot->getId();

        try
        {
            $elementTypeManager = Makeweb_Elementtypes_Elementtype_Manager::getInstance();

            if (!$title)
            {
                $title = $siteroot->getTitle('en');
            }

            $sysRoot = $elementTypeManager->create(
                Makeweb_Elementtypes_Elementtype_Version::TYPE_STRUCTURE,
                'WWW-Root ' . $title,
                'www_root.gif'
            );
            $sysRootVersion = $sysRoot->getLatest();

            $elementTypeId      = $sysRoot->getId(); //$row['element_type_id'];
            $elementTypeVersion = $sysRootVersion->getVersion(); //$row['version'];

            $insertData = array(
                'element_type_id'  => $elementTypeId,
                'create_uid'       => MWF_Env::getUid(),
                'create_time'      => $db->fn->now(),
            );

            $db->insert($db->prefix . 'element', $insertData);

            $eid = $db->lastInsertId($db->prefix . 'element');

            $insertData = array(
                'eid'                  => $eid,
                'version'              => 1,
                'element_type_id'      => $elementTypeId,
                'element_type_version' => $elementTypeVersion,
                'create_uid'           => MWF_Env::getUid(),
                'create_time'          => $db->fn->now(),
            );

            $db->insert($db->prefix . 'element_version', $insertData);

            // create element in tree
            $tree = new Tree($siterootID);
            $tree->add(null, $eid);
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }
}