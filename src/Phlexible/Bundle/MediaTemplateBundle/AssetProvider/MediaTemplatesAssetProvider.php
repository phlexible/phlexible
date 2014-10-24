<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Media templates asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTemplatesAssetProvider implements AssetProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        return array(
            '@PhlexibleMediaTemplateBundle/Resources/scripts/Definitions.js',

            '@PhlexibleMediaTemplateBundle/Resources/scripts/MainPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/BasePreviewPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/TemplatesGrid.js',

            '@PhlexibleMediaTemplateBundle/Resources/scripts/image/FormPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/image/PreviewPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/image/MainPanel.js',

            '@PhlexibleMediaTemplateBundle/Resources/scripts/video/FormPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/video/PreviewPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/video/MainPanel.js',

            '@PhlexibleMediaTemplateBundle/Resources/scripts/audio/FormPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/audio/PreviewPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/audio/MainPanel.js',

            '@PhlexibleMediaTemplateBundle/Resources/scripts/pdf2swf/FormPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/pdf2swf/PreviewPanel.js',
            '@PhlexibleMediaTemplateBundle/Resources/scripts/pdf2swf/MainPanel.js',

            '@PhlexibleMediaTemplateBundle/Resources/scripts/menuhandle/MediaTemplatesHandle.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return array(
            '@PhlexibleMediaTemplateBundle/Resources/styles/mediatemplates.css',
        );
    }
}
