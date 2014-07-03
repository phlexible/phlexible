<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\Teaser;

/**
 * Teaser history
 *
 * @author Stephan Makeweb_Teasers <sw@brainbits.net>
 */
class TeaserHistory
{
    const ACTION_CREATE_TEASER = 'createTeaser';
    const ACTION_DELETE_TEASER = 'deleteTeaser';
    const ACTION_CREATE_INSTANCE = 'createInstance';
    const ACTION_PUBLISH = 'publish';
    const ACTION_SET_OFFLINE = 'setOffline';

    /**
     * Insert new history entry
     *
     * @param string $action
     * @param string $teaserId
     * @param string $eid
     * @param string $version
     * @param string $language
     * @param string $comment
     */
    public static function insert($action, $teaserId, $eid, $version = null, $language = null, $comment = null)
    {
        $db = MWF_Registry::getContainer()->dbPool->default;

        $insertData = array(
            'teaser_id'   => $teaserId,
            'eid'         => $eid,
            'language'    => $language,
            'version'     => $version,
            'action'      => $action,
            'comment'     => $comment,
            'create_time' => $db->fn->now(),
            'create_uid'  => MWF_Env::getUid(),
        );

        $db->insert($db->prefix . 'element_tree_teasers_history', $insertData);
    }
}
