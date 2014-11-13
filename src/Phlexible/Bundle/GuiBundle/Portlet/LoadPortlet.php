<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Portlet;

use Phlexible\Bundle\DashboardBundle\Portlet\Portlet;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Load lortlet
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class LoadPortlet extends Portlet
{
    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this
            ->setId('load-portlet')
            ->setTitle($translator->trans('gui.server_load', [], 'gui'))
            ->setClass('Phlexible.gui.portlet.Load')
            ->setIconClass('p-gui-load-icon');
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (function_exists('sys_getloadavg')) {
            $data = sys_getloadavg();
        } else {
            $data = [0, 0, 0];
        }

        return $data;
    }
}
