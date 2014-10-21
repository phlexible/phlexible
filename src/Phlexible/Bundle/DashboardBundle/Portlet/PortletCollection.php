<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DashboardBundle\Portlet;

/**
 * Portlet collection
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class PortletCollection
{
    /**
     * @var array
     */
    private $portlets = array();

    /**
     * @param Portlet[] $portlets
     */
    public function __construct(array $portlets)
    {
        foreach ($portlets as $portlet) {
            $this->add($portlet);
        }
    }

    /**
     * @param Portlet $portlet
     *
     * @return $this
     */
    public function add(Portlet $portlet)
    {
        $this->portlets[] = $portlet;

        return $this;
    }

    /**
     * @return Portlet[]
     */
    public function all()
    {
        return $this->portlets;
    }
}
