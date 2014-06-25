<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\LockBundle\Portlet;

use Doctrine\ORM\EntityManager;
use Phlexible\Bundle\DashboardBundle\Portlet\AbstractPortlet;
use Phlexible\Bundle\LockBundle\Entity\Lock;
use Phlexible\Bundle\LockBundle\Model\LockManagerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * My locks portlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MyLocksPortlet extends AbstractPortlet
{
    /**
     * @var LockManagerInterface
     */
    private $lockManager;

    /**
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * @param TranslatorInterface      $translator
     * @param LockManagerInterface     $lockManager
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(TranslatorInterface $translator,
                                LockManagerInterface $lockManager,
                                SecurityContextInterface $securityContext)
    {
        $this->id        = 'locks-portlet';
        $this->title     = $translator->trans('locks.my_locked_items', array(), 'gui');
        $this->class     = 'Phlexible.locks.portlet.Locks';
        $this->iconClass = 'p-lock-lock-icon';

        $this->lockManager = $lockManager;
        $this->securityContext = $securityContext;
    }

    /**
     * Return Portlet data
     *
     * @return array
     */
    public function getData()
    {
        $locks = $this->lockManager
            ->findBy(array('userId' => $this->securityContext->getToken()->getUser()->getId()));

        $data = array();
        foreach ($locks as $lock) {
            /* @var $lock Lock */
            $data[] = array(
                'ident'       => $lock->getId(),
                'lock_type'   => $lock->getType(),
                'object_type' => $lock->getObjectType(),
                'object_id'   => $lock->getObjectId(),
                'lock_time'   => $lock->getLockedAt()->format('Y-m-d H:i:s'),
            );
        }

        return $data;
    }
}
