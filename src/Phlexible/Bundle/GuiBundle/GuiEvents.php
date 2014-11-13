<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle;

/**
 * Frame events
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class GuiEvents
{
    /**
     * Get config event
     */
    const GET_CONFIG = 'phlexible_gui.get_config';

    /**
     * Get menu event
     */
    const GET_MENU = 'phlexible_gui.get_menu';

    /**
     * View frame event
     */
    const VIEW_FRAME = 'phlexible_gui.view_frame';
}
