<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ElementBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Phlexible\Bundle\ElementBundle\ElementsMessage;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Element repository
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ElementRepository extends EntityRepository
{
    /**
     * Save element
     *
     * @param Element $element
     *
     * @return $this
     */
    public function save(Element $element)
    {
        if (!$element->getEid()) {
            $event = new BeforeCreateEvent($element);
            if (!$this->dispatcher->dispatch($event)) {
                throw new \Exception('Create canceled by callback.');
            }

            $this->loader->insert($elementtype);

            // post event
            $event = new CreateEvent($elementtype);
            $this->dispatcher->dispatch($event);

            // post message
            $message = new ElementtypesMessage('Element Type "' . $elementtype->getId() . ' created.');
            $message->post();
        } else {
            $this->loader->update($element);

            // post message
            $message = new ElementtypesMessage('Element Type "' . $elementtype->getId() . ' updated.');
            $message->post();
        }

        return $this;
    }

    /**
     * Delete element
     *
     * @param Element $element
     *
     * @return $this
     * @throws \Exception
     */
    public function delete(Element $element)
    {
        // post before event
        $event = new BeforeDeleteEvent($elementtype);
        if (!$this->dispatcher->dispatch($event)) {
            throw new \Exception('Delete canceled by listener.');
        }

        $delete = true;

        if ($elementtype->getType() == ElementtypeVersion::TYPE_REFERENCE) {
            $db = MWF_Registry::getContainer()->dbPool->default;
            $select = $db->select()
                ->distinct()
                ->from(
                    $db->prefix . 'elementtype_structure',
                    array('element_type_id', new Zend_Db_Expr('MAX(version) AS max_version'))
                )
                ->where('reference_id = ?', $elementtype->getId())
                ->group('element_type_id');

            $result = $db->fetchAll($select);

            if (count($result)) {
                $delete = false;

                $select = $db->select()
                    ->from($db->prefix . 'elementtype', 'latest_version')
                    ->where('element_type_id = ?');

                foreach ($result as $row) {
                    $latestElementTypeVersion = $db->fetchOne($select, $row['element_type_id']);

                    if ($latestElementTypeVersion == $row['max_version']) {
                        throw new \Exception('Reference in use, can\'t delete.');
                    }
                }
            }
        }

        if ($delete) {
            $this->loader->delete($elementtype);

            // send message
            $message = new ElementsMessage('Element type "' . $elementtype->getId() . '" deleted.');
            $message->post();
        } else {
            $this->loader->softDelete($elementtype);

            // send message
            $message = new ElementsMessage('Element type "' . $elementtype->getId() . '" soft deleted.');
            $message->post();
        }

        // post event
        $event = new DeleteEvent($elementtype);
        $this->dispatcher->dispatch($event);

        return $this;
    }
}
