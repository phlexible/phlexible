<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\AbstractPortlet;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Load lortlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LoadPortlet extends AbstractPortlet
{
    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->id        = 'load-portlet';
        $this->title     = $translator->trans('gui.server_load', array(), 'gui');
        $this->class     = 'Phlexible.gui.portlet.Load';
        $this->iconClass = 'p-gui-load-icon';
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (function_exists('sys_getloadavg'))
        {
            $data = sys_getloadavg();
        }
        else
        {
            $data = array(0,0,0);
        }

        return $data;
    }
}
