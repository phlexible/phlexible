<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementtypeBundle\Elementtype;

use Phlexible\Bundle\ElementtypeBundle\Elementtype\Loader\LoaderInterface;
use Phlexible\Bundle\ElementtypeBundle\ElementtypeEvents;
use Phlexible\Bundle\ElementtypeBundle\ElementtypesMessage;
use Phlexible\Bundle\ElementtypeBundle\Event\ElementtypeEvent;
use Phlexible\Bundle\MessageBundle\Message\MessagePoster;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Elementtype repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementtypeRepository
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var MessagePoster
     */
    private $messageService;

    /**
     * @var ElementtypeCollection
     */
    private $elementtypes;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param LoaderInterface          $loader
     * @param MessagePoster           $messageService
     */
    public function __construct(EventDispatcherInterface $dispatcher, LoaderInterface $loader, MessagePoster $messageService)
    {
        $this->dispatcher = $dispatcher;
        $this->loader = $loader;
        $this->messageService = $messageService;
    }

    /**
     * @return ElementtypeCollection
     */
    public function getCollection()
    {
        if (null === $this->elementtypes) {
            $this->elementtypes = $this->loader->load();
        }

        return $this->elementtypes;
    }

    /**
     * Find element type by ID
     *
     * @param int $elementtypeId
     *
     * @return Elementtype
     */
    public function find($elementtypeId)
    {
        return $this->getCollection()->get($elementtypeId);
    }

    /**
     * Find element type by unique ID
     *
     * @param string $uniqueID
     * @return Elementtype
     */
    public function findByUniqueID($uniqueID)
    {
        return $this->getCollection()->getByUniqueId($uniqueID);
    }

    /**
     * Find element types by type
     *
     * @param string $type
     * @return Elementtype[]
     */
    public function findByType($type)
    {
        return $this->getCollection()->getByType($type);
    }

    /**
     * Find all element types
     *
     * @return Elementtype[]
     */
    public function findAll()
    {
        return $this->getCollection()->getAll();
    }

    /**
     * Save element type
     *
     * @param Elementtype $elementtype
     * @throws \Exception
     *
     * @return $this
     */
    public function save(Elementtype $elementtype)
    {
        if (!$elementtype->getId()) {
            $event = new ElementtypeEvent($elementtype);
            if (!$this->dispatcher->dispatch(ElementtypeEvents::BEFORE_CREATE, $event)) {
                throw new \Exception('Create canceled by callback.');
            }

            $this->loader->insert($elementtype);

            $event = new ElementtypeEvent($elementtype);
            $this->dispatcher->dispatch(ElementtypeEvents::CREATE, $event);

            // post message
            $message = ElementtypesMessage::create('Element Type "'.$elementtype->getId().' created.');
            $this->messageService->post($message);
        } else {
            $event = new ElementtypeEvent($elementtype);
            if (!$this->dispatcher->dispatch(ElementtypeEvents::BEFORE_UPDATE, $event)) {
                throw new \Exception('Create canceled by callback.');
            }

            $this->loader->update($elementtype);

            $event = new ElementtypeEvent($elementtype);
            $this->dispatcher->dispatch(ElementtypeEvents::UPDATE, $event);

            // post message
            $message = ElementtypesMessage::create('Element Type "'.$elementtype->getId().' updated.');
            $this->messageService->post($message);
        }

        return $this;
    }

    /**
     * Delete an Element Type
     *
     * @param Elementtype $elementtype
     * @return $this
     * @throws ElementtypeException
     */
    public function delete(Elementtype $elementtype)
    {

        // post before event
        $event = new ElementtypeEvent($elementtype);
        if (!$this->dispatcher->dispatch(ElementtypeEvents::BEFORE_DELETE, $event)) {
            throw new ElementtypeException('Delete canceled by listener.');
        }

        $delete = true;

        if ($elementtype->getType() == ElementtypeVersion::TYPE_REFERENCE)
        {
            $db = MWF_Registry::getContainer()->dbPool->default;
            $select = $db->select()
                         ->distinct()
                         ->from($db->prefix.'elementtype_structure', array('elementtype_id', new Zend_Db_Expr('MAX(version) AS max_version')))
                         ->where('reference_id = ?', $elementtype->getId())
                         ->group('elementtype_id');

            $result = $db->fetchAll($select);

            if (count($result))
            {
                $delete = false;

                $select = $db->select()
                             ->from($db->prefix . 'elementtype', 'latest_version')
                             ->where('elementtype_id = ?');

                foreach($result as $row)
                {
                    $latestElementTypeVersion = $db->fetchOne($select, $row['elementtype_id']);

                    if ($latestElementTypeVersion == $row['max_version'])
                    {
                        throw new ElementtypeException('Reference in use, can\'t delete.');
                    }
                }
            }
        }

        if ($delete)
        {
            $this->loader->delete($elementtype);

            // send message
            $message = ElementtypesMessage::create('Element type "'.$elementtype->getId().'" deleted.');
            $this->messageService->post($message);
        }
        else
        {
            $this->loader->softDelete($elementtype);

            // send message
            $message = ElementtypesMessage::create('Element type "'.$elementtype->getId().'" soft deleted.');
            $this->messageService->post($message);
        }

        // post event
        $event = new ElementtypeEvent($elementtype);
        $this->dispatcher->dispatch(ElementtypeEvents::DELETE, $event);

        return $this;
    }
}
