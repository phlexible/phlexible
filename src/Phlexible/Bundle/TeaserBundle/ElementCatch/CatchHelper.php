<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ElementCatch;

use Doctrine\DBAL\Connection;
use Phlexible\Bundle\ElementBundle\ElementService;
use Phlexible\Bundle\ElementBundle\Entity\Element;
use Phlexible\Bundle\ElementBundle\Entity\ElementVersion;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Catch helper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CatchHelper
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var ElementService
     */
    private $elementService;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param Connection               $connection
     * @param ElementService           $elementService
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(Connection $connection, ElementService $elementService, EventDispatcherInterface $dispatcher)
    {
        $this->connection = $connection;
        $this->elementService = $elementService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param int $treeId
     */
    public function removeByTid($treeId)
    {
        $this->connection->delete(
            'catch_lookup_meta',
            array(
                'tree_id' => $treeId
            )
        );

        $this->connection->delete(
            'catch_lookup_meta',
            array(
                'tree_id' => $treeId
            )
        );
    }

    /**
     * @param int $eid
     */
    public function removeByEid($eid)
    {
        $this->connection->delete(
            'catch_lookup_element',
            array(
                'eid' => $eid
            )
        );

        $this->connection->delete(
            'catch_lookup_meta',
            array(
                'eid' => $eid
            )
        );
    }

    /**
     * @param int $treeId
     */
    public function removePreviewByTid($treeId)
    {
        $this->connection->delete(
            'catch_lookup_element',
            array(
                'tree_id' => $treeId,
                'preview' => 1,
            )
        );
    }

    /**
     * @param int $eid
     */
    public function removePreviewByEid($eid)
    {
        $this->connection->delete(
            'catch_lookup_element',
            array(
                'eid'     => $eid,
                'preview' => 1,
            )
        );
    }

    /**
     * @param int $treeId
     */
    public function removeOnlineByTid($treeId)
    {
        $this->connection->delete(
            'catch_lookup_element',
            array(
                'tree_id' => $treeId,
                'preview' => 0,
            )
        );
    }

    /**
     * @param int $eid
     */
    public function removeOnlineByEid($eid)
    {
        $this->connection->delete(
            'catch_lookup_element',
            array(
                'eid'     => $eid,
                'preview' => 0,
            )
        );
    }

    /**
     * @param int    $treeId
     * @param string $language
     */
    public function removeOnlineByTidAndLanguage($treeId, $language)
    {
        $this->connection->delete(
            'catch_lookup_element',
            array(
                'tree_id'  => $treeId,
                'language' => $language,
                'preview'  => 0,
            )
        );
    }

    /**
     * @param int    $eid
     * @param string $language
     */
    public function removeOnlineByEidAndLanguage($eid, $language)
    {
        $this->connection->delete(
            'catch_lookup_element',
            array(
                'eid'      => $eid,
                'language' => $language,
                'preview'  => 0,
            )
        );
    }

    /**
     * @param int    $treeId
     * @param int    $version
     * @param string $language
     */
    public function removeMetaByTidAndVersionAndLanguage($treeId, $version, $language)
    {
        $this->connection->delete(
            'catch_lookup_meta',
            array(
                'tree_id'  => $treeId,
                'version'  => $version,
                'language' => $language,
            )
        );
    }

    /**
     * @param int    $eid
     * @param int    $version
     * @param string $language
     */
    public function removeMetaByEidAndVersionAndLanguage($eid, $version, $language)
    {
        $this->connection->delete(
            'catch_lookup_meta',
            array(
                'eid'      => $eid,
                'version'  => $version,
                'language' => $language,
            )
        );
    }

    /**
     * @param int $eid
     */
    public function update($eid)
    {
        // TODO: repair
        return;
        $this->updateOnline($eid);
        $this->updatePreview($eid);
    }

    /**
     * @param int $eid
     *
     * @return int|null
     */
    public function updatePreview($eid)
    {
        // TODO: repair
        return;
        $event = new BeforeUpdateCatchTeaserHelper($eid, true);
        if ($this->dispatcher->dispatch($event)->isPropagationStopped()) {
            return null;
        }

        $this->removePreviewByEid($eid);

        $element = $this->elementService->findElement($eid);

        // Preview version
        $select = $this->connection
            ->select()
            ->distinct()
            ->from(
                array('et' => 'element_tree'),
                array('eid', 'id', 'modify_time')
            )
            ->join(
                array('e' => 'element'),
                'et.eid = e.eid',
                array('latest_version')
            )
            ->join(
                array('ed' => 'element_data'),
                'et.eid = ed.eid',
                array()
            )
            ->join(
                array('edl' => 'element_data_language'),
                'ed.eid = edl.eid' .
                ' AND ed.data_id = edl.data_id' .
                ' AND ed.version = edl.version' .
                ' AND edl.version = e.latest_version',
                array('language')
            )
            ->join(
                array('etp' => 'element_tree_page'),
                'et.id = etp.tree_id AND ' .
                'etp.version = edl.version',
                array('navigation', 'restricted')
            )
            ->where('et.eid = ?', $eid);

        $nodes = $this->_db->fetchAll($select);

        foreach ($nodes as $node) {
            $elementVersion = $this->elementService->findElementVersion($element, $node['latest_version']);

            $this->updateVersion(
                $element,
                $elementVersion,
                $node['id'],
                $node['modify_time'],
                true,
                $node['language'],
                $node['navigation'],
                $node['restricted'],
                null //#$node['online_version']
            );
        }

        $event = new UpdateCatchTeaserHelper($eid, true);
        $this->dispatcher->dispatch($event);

        return count($nodes);
    }

    /**
     * @param int $eid
     *
     * @return int|null
     */
    public function updateOnline($eid)
    {
        // TODO: repair
        #return;
        $event = new BeforeUpdateCatchTeaserHelper($eid);
        if ($this->dispatcher->dispatch($event)->isPropagationStopped()) {
            return null;
        }

        $this->removeOnlineByEid($eid);

        $element = $this->elementService->findElement($eid);

        // Live version
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from(
                array('eto' => 'element_tree_online'),
                array('tree_id', 'version', 'publish_time', 'language')
            )
            ->join(
                array('etp' => 'element_tree_page'),
                'eto.tree_id = etp.tree_id AND ' .
                'eto.version = etp.version',
                array('restricted', 'navigation')
            )
            ->where('eto.eid = ?', $eid);

        $nodes = $this->connection->fetchAll($qb->getSQL());

        foreach ($nodes as $node) {
            $elementVersion = $this->elementService->findElementVersion($element, $node['version']);

            $this->updateVersion(
                $element,
                $elementVersion,
                $node['tree_id'],
                $node['publish_time'],
                false,
                $node['language'],
                $node['navigation'],
                $node['restricted'],
                null
            );
        }

        $event = new UpdateCatchTeaserHelper($eid);
        $this->dispatcher->dispatch($event);

        return count($nodes);
    }

    /**
     * @param Element        $element
     * @param ElementVersion $elementVersion
     * @param int            $tid
     * @param int            $time
     * @param bool           $preview
     * @param string         $language
     * @param bool           $navigation
     * @param bool           $restricted
     * @param int            $onlineVersion
     */
    protected function updateVersion(
        Element $element,
        ElementVersion $elementVersion,
        $tid,
        $time,
        $preview,
        $language,
        $navigation,
        $restricted,
        $onlineVersion)
    {
        $eid = $element->getEid();
        $version = $elementVersion->getVersion();
        $customDate = $elementVersion->getCustomDate($language);

        // fill catchteaser_metaset_itmes

        $this->updateMeta($tid, $eid, $version, $language);

        // fill catchteaser_helper

        $insertData = array(
            'eid'            => $eid,
            'tid'            => $tid,
            'publish_time'   => $time,
            'custom_date'    => $customDate,
            'preview'        => $preview ? 1 : 0,
            'elementtype_id' => $element->getElementTypeId(),
            'version'        => $version,
            'language'       => $language,
            'online_version' => $onlineVersion,
            'in_navigation'  => (int) $navigation,
            'restricted'     => (int) $restricted,
        );

        $this->connection->insert('catch_lookup_element', $insertData);
    }

    /**
     * @param int    $tid
     * @param int    $eid
     * @param int    $version
     * @param string $language
     */
    protected function updateMeta($tid, $eid, $version, $language)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('*')
            ->from('element_version_metaset_items', 'm')
            ->where('eid = ?', $eid)
            ->andWhere('version = ?', $version)
            ->andWhere('language = ?', $language)
            ->andWhere('value IS NOT NULL')
            ->andWhere('value != ""');

        $metasetItems = $this->connection->fetchAll($qb->getSQL());

        $this->removeMetaByEidAndVersionAndLanguage($eid, $version, $language);

        foreach ($metasetItems as $metaset) {
            $cleanString = str_replace(
                array(',', ';'),
                array('===', '==='),
                html_entity_decode($metaset['value'], ENT_COMPAT, 'UTF-8')
            );

            $tmp = explode('===', $cleanString);

            foreach ($tmp as $item) {
                $metaset['tid'] = $tid;
                $metaset['value'] = mb_strtolower(trim($item));
                $this->connection->insert('catch_lookup_meta', $metaset);
            }
        }
    }
}
