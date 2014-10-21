<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\Controller;

use Phlexible\Bundle\GuiBundle\Response\ResultResponse;
use Phlexible\Bundle\MessageBundle\Criteria\Criteria;
use Phlexible\Bundle\MessageBundle\Criteria\Criterium;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Filter controller
 *
 * @author Caspar Baratella <cb@brainbits.net>
 * @Route("/messages/filters")
 */
class FiltersController extends Controller
{
    /**
     * List filters
     *
     * @return JsonResponse
     * @Route("", name="messages_filters")
     */
    public function listAction()
    {
        $filterManager = $this->get('phlexible_message.filter_manager');

        $filters = array();

        foreach ($filterManager->findBy(array('userId' => $this->getUser()->getId())) as $filter) {
            $criteria = array();
            foreach ($filter->getCriteria() as $groupIndex => $group) {
                foreach ($group as $criterium) {
                    $criteria[] = array(
                        'criteria' => $criterium->getType(),
                        'value'    => $criterium->getValue(),
                        'group'    => $groupIndex + 1,
                    );
                }
            }

            $filters[] = array(
                'id'       => $filter->getId(),
                'title'    => $filter->getTitle(),
                'criteria' => $criteria,
            );
        }

        return new JsonResponse($filters);
    }

    /**
     * List filter values
     *
     * @return JsonResponse
     * @Route("/filtervalues", name="messages_filter_filtervalues")
     */
    public function filtervalueAction()
    {
        $data = array('roles' => array());

        /*
        foreach ($acl->getResources() as $role) {
            $data['roles'][] = array($role, ucfirst($role));
        }
        */

        $bundles = $this->container->getParameter('kernel.bundles');
        foreach ($bundles as $id => $class) {
            $data['bundles'][] = array($id, $id);
        }

        $data['criteria'] = array(
            array('key' => 'subject_like', 'value' => 'Subject like'),
            array('key' => 'subject_not_like', 'value' => 'Subject not like'),
            array('key' => 'body_like', 'value' => 'Body Like'),
            array('key' => 'body_not_like', 'value' => 'Body not like'),
            array('key' => 'priority_is', 'value' => 'Priority is'),
            array('key' => 'priority_in', 'value' => 'Priority in'),
            array('key' => 'priority_min', 'value' => 'Priority min'),
            array('key' => 'type_is', 'value' => 'Type is'),
            array('key' => 'type_in', 'value' => 'Type in'),
            array('key' => 'channel_is', 'value' => 'Channel is'),
            array('key' => 'channel_like', 'value' => 'Channel like'),
            array('key' => 'channel_in', 'value' => 'Channel in'),
            array('key' => 'role_is', 'value' => 'Role is'),
            array('key' => 'min_age', 'value' => 'Min age'),
            array('key' => 'max_age', 'value' => 'Max age'),
            array('key' => 'start_date', 'value' => 'Start date'),
            array('key' => 'end_date', 'value' => 'End date'),
            array('key' => 'date_is', 'value' => 'Date is'),
            array('key' => '', 'value' => ''),
        );

        return new JsonResponse($data);
    }

    /**
     * Create filter
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/create", name="messages_filter_create")
     */
    public function createAction(Request $request)
    {
        $title = $request->get('title');

        $filterManager = $this->get('phlexible_message.filter_manager');

        $filter = $filterManager->create();
        $filter
            ->setUserId($this->getUser()->getId())
            ->setTitle($title)
            ->setModifiedAt(new \DateTime())
            ->setCreatedAt(new \DateTime());

        $filterManager->updateFilter($filter);

        return new ResultResponse(true, 'Filter created.');
    }

    /**
     * Updates a Filter
     *
     * @param Request $request
     * @param string  $id
     *
     * @return ResultResponse
     * @Route("/update/{id}", name="messages_filter_update")
     */
    public function updateAction(Request $request, $id)
    {
        $title = $request->get('title');
        $rawCriteria = json_decode($request->get('criteria'), true);

        $filterManager = $this->get('phlexible_message.filter_manager');

        $filter = $filterManager->find($id);
        $filter->setTitle($title);

        $criteria = new Criteria();
        $criteria->setMode(Criteria::MODE_OR);
        foreach ($rawCriteria as $group) {
            $criteriaGroup = new Criteria();
            $criteriaGroup->setMode(Criteria::MODE_AND);
            foreach ($group as $row) {
                if (!strlen($row['value'])) {
                    continue;
                }

                $criterium = new Criterium($row['key'], $row['value']);
                $criteriaGroup->add($criterium);
            }
            if ($criteriaGroup->count()) {
                $criteria->addCriteria($criteriaGroup);
            }
        }
        $filter->setCriteria($criteria);

        $filterManager->updateFilter($filter);

        return new ResultResponse(true, 'Filter updated');
    }

    /**
     * Delete filter
     *
     * @param string $id
     *
     * @return ResultResponse
     * @Route("/delete/{id}", name="messages_filter_delete")
     */
    public function deleteAction($id)
    {
        $filterManager = $this->get('phlexible_message.filter_manager');
        $filter = $filterManager->find($id);
        $filterManager->deleteFilter($filter);

        return new ResultResponse(true, 'Filter "' . $filter->getTitle() . '" deleted.');
    }

    /**
     * Preview messages
     *
     * @param Request $request
     *
     * @return ResultResponse
     * @Route("/preview", name="messages_filter_preview")
     */
    public function previewAction(Request $request)
    {
        $rawCriteria = json_decode($request->get('filters'), true);

        $messageManager = $this->get('phlexible_message.message_manager');

        $criteria = new Criteria();
        $criteria->setMode(Criteria::MODE_OR);
        foreach ($rawCriteria as $group) {
            $criteriaGroup = new Criteria();
            $criteriaGroup->setMode(Criteria::MODE_AND);
            foreach ($group as $row) {
                if (!strlen($row['value'])) {
                    continue;
                }

                $criterium = new Criterium($row['key'], $row['value']);
                $criteriaGroup->add($criterium);
            }
            if ($criteriaGroup->count()) {
                $criteria->addCriteria($criteriaGroup);
            }
        }

        if (!$criteria->count()) {
            return new JsonResponse(array(
                'total'    => 0,
                'messages' => array()
            ));
        }

        $messages = $messageManager->findByCriteria($criteria, array('createdAt' => 'DESC'), 20);
        $count = $messageManager->countByCriteria($criteria);

        $priorityList = $messageManager->getPriorityNames();
        $typeList = $messageManager->getTypeNames();

        $data = array();
        foreach ($messages as $message) {
            $data[] = array(
                'subject'    => $message->getSubject(),
                'body'       => nl2br($message->getBody()),
                'priority'   => $priorityList[$message->getPriority()],
                'type'       => $typeList[$message->getType()],
                'channel'    => $message->getChannel(),
                'role'       => $message->getRole(),
                'created_at' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                'user'       => $message->getUser(),
            );
        }

        return new JsonResponse(array(
            'total'    => $count,
            'messages' => $data
        ));
    }
}
