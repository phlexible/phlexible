<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\TeaserBundle\ElementCatch;

use Phlexible\Bundle\GuiBundle\Util\Uuid;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Phlexible\Bundle\TeaserBundle\Event\ElementCatchEvent;
use Phlexible\Bundle\TeaserBundle\TeaserEvents;
use Phlexible\Bundle\TeaserBundle\TeasersMessage;
use Phlexible\Component\Database\ConnectionManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element catch repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementCatchRepository
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    private $db;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var MessagePoster
     */
    private $messageService;

    /**
     * @param ConnectionManager        $connectionManager
     * @param EventDispatcherInterface $dispatcher
     * @param MessagePoster            $messageService
     */
    public function __construct(
        ConnectionManager $connectionManager,
        EventDispatcherInterface $dispatcher,
        MessagePoster $messageService)
    {
        $this->db = $connectionManager->default;
        $this->dispatcher = $dispatcher;
        $this->messageService = $messageService;
    }

    /**
     * @param string $id
     *
     * @return ElementCatch
     */
    public function find($id)
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'catch')
            ->where('id = ?', $id);

        $row = $this->db->fetchRow($select);

        if (!$row) {
            throw new \Exception("gnarf");
        }

        return $this->mapRow($row);
    }

    /**
     * @return ElementCatch[]
     */
    public function findAll()
    {
        $select = $this->db
            ->select()
            ->from($this->db->prefix . 'catch');

        $rows = $this->db->fetchAll($select);

        return $this->mapRows($rows);
    }

    /**
     * @param ElementCatch $catch
     */
    public function save(ElementCatch $catch)
    {
        if ($catch->getId()) {
            // before save event
            $event = new ElementCatchEvent($catch);
            if (!$this->dispatcher->dispatch(TeaserEvents::BEFORE_UPDATE_CATCH, $event)) {
                return;
            }

            $this->db->update(
                $this->db->prefix . 'catch',
                array(
                    'title'            => $catch->getTitle(),
                    'tree_id'          => $catch->getTreeId(),
                    'elementtype_ids'  => !empty($catch->getElementtypeIds()) ? explode(
                            ',',
                            $catch->getElementtypeIds()
                        ) : null,
                    'sort_field'       => $catch->getSortField(),
                    'sort_order'       => $catch->getSortOrder(),
                    'in_navigation'    => $catch->inNavigation() ? 1 : 0,
                    'max_depth'        => $catch->getMaxDepth(),
                    'max_results'      => $catch->getMaxResults(),
                    'results_per_page' => $catch->getResultsPerPage(),
                    'rotation'         => $catch->hasRotation(),
                    'filter'           => $catch->getFilter(),
                    'pool_size'        => $catch->getPoolSize(),
                    'template'         => $catch->getTemplate(),
                    'meta_search'      => json_encode($catch->getMetaSearch())
                ),
                array(
                    'id' => $catch->getId(),
                )
            );

            // save event
            $event = new ElementCatchEvent($catch);
            $this->dispatcher->dispatch(TeaserEvents::UPDATE_CATCH, $event);

            // post cleartext message
            $message = TeasersMessage::create('Catch updated.');
            $this->messageService->post($message);
        } else {
            $event = new ElementCatchEvent($catch);
            if (!$this->dispatcher->dispatch(TeaserEvents::BEFORE_CREATE_CATCH, $event)) {
                return;
            }

            $catch->setId(Uuid::generate());

            $this->db->insert(
                $this->db->prefix . 'catch',
                array(
                    'id'               => $catch->getId(),
                    'title'            => $catch->getTitle(),
                    'tree_id'          => $catch->getTreeId(),
                    'elementtype_ids'  => !empty($catch->getElementtypeIds()) ? explode(
                            ',',
                            $catch->getElementtypeIds()
                        ) : null,
                    'sort_field'       => $catch->getSortField(),
                    'sort_order'       => $catch->getSortOrder(),
                    'in_navigation'    => $catch->inNavigation() ? 1 : 0,
                    'max_depth'        => $catch->getMaxDepth(),
                    'max_results'      => $catch->getMaxResults(),
                    'results_per_page' => $catch->getResultsPerPage(),
                    'rotation'         => $catch->hasRotation(),
                    'filter'           => $catch->getFilter(),
                    'pool_size'        => $catch->getPoolSize(),
                    'template'         => $catch->getTemplate(),
                    'meta_search'      => json_encode($catch->getMetaSearch())
                )
            );

            // save event
            $event = new ElementCatchEvent($catch);
            $this->dispatcher->dispatch(TeaserEvents::CREATE_CATCH, $event);

            // post cleartext message
            $message = TeasersMessage::create('Catch created.');
            $this->messageService->post($message);
        }
    }

    /**
     * @param ElementCatch $catch
     */
    public function delete(ElementCatch $catch)
    {
        if ($catch->getId() === null) {
            return;
        }

        // post before delete event
        $event = new ElementCatchEvent($catch);
        if (!$this->dispatcher->dispatch(TeaserEvents::BEFORE_DELETE_CATCH, $event)) {
            return;
        }

        $this->db->delete(
            $this->db->prefix . 'catch',
            array(
                'id = ?' => $catch->getId()
            )
        );

        // post delete event
        $event = new ElementCatchEvent($catch);
        $this->dispatcher->dispatch(TeaserEvents::DELETE_CATCH, $event);

        // post cleartext message
        $message = TeasersMessage::create('Catch deleted.');
        $this->messageService->post($message);
    }

    /**
     * @param array $rows
     *
     * @return ElementCatch[]
     */
    private function mapRows(array $rows)
    {
        $catches = array();

        foreach ($rows as $row) {
            $catches[] = $this->mapRow($row);
        }

        return $catches;
    }

    /**
     * @param array $row
     *
     * @return ElementCatch
     */
    private function mapRow(array $row)
    {
        $catch = new ElementCatch();
        $catch
            ->setId($row['id'])
            ->setTitle($row['title'])
            ->setTreeId($row['tree_id'])
            ->setFilter($row['filter'])
            ->setMaxDepth($row['max_depth'])
            ->setMaxResults($row['max_results'])
            ->setMetaSearch($row['meta_search'] ? json_decode($row['meta_search'], true) : null)
            ->setNavigation($row['in_navigation'])
            ->setPoolSize($row['pool_size'])
            ->setResultsPerPage($row['results_per_page'])
            ->setRotation($row['rotation'])
            ->setSortField($row['sort_field'])
            ->setSortOrder($row['sort_order'])
            ->setTemplate($row['template'])
            ->setElementtypeIds(explode(',', $row['elementtype_ids']));

        return $catch;
    }
}
