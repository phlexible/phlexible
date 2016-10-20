<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
