<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\FrontendMediaBundle\EventListener;

/**
 * Render listener
 *
 * @author Michael van Engelshoven <mve@brainbits.net>
 */
class RenderListener
{
    public function onElementtypeDocumentlist(Makeweb_Renderers_Event_Elementtype $event)
    {
        // TODO: parameter alle ans template übergeben? erspart callback...

        $renderer = $event->getRenderer();
        $request  = $renderer->getRequest();

        if ($request->hasParam('fileid'))
        {
            $view = $renderer->getView();
            $view->assign('fileid', $request->getParam('fileid'));
        }
    }
}
