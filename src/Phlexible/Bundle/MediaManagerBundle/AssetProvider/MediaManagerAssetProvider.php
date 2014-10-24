<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\AssetProvider;

use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * Media manager asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaManagerAssetProvider implements AssetProviderInterface
{

    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        return array(
            //'@PhlexibleMediaManagerBundle/Resources/scripts/ux/plupload.js',
            //'@PhlexibleMediaManagerBundle/Resources/scripts/ux/moxie.js',
            //'@PhlexibleMediaManagerBundle/Resources/scripts/ux/plupload.dev.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/ux/plupload.full.min.js',
            //'@PhlexibleMediaManagerBundle/Resources/scripts/ux/SwfUpload.js',
            //'@PhlexibleMediaManagerBundle/Resources/scripts/ux/Ext.ux.SwfUploadPanel.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/ux/Ext.ux.LocationBar.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        return array(
            '@PhlexibleMediaManagerBundle/Resources/styles/SwfUploadPanel.css',
            '@PhlexibleMediaManagerBundle/Resources/styles/Ext.ux.LocationBar.css',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        return array(
            '@PhlexibleMediaManagerBundle/Resources/scripts/Definitions.js',

            '@PhlexibleMediaManagerBundle/Resources/scripts/model/File.js',

            '@PhlexibleMediaManagerBundle/Resources/scripts/util/Bullets.js',

            '@PhlexibleMediaManagerBundle/Resources/scripts/Templates.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FolderTreeNodeUI.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FolderTree.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FilesGrid.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/AttributesPanel.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FilePreviewPanel.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FileVersionsPanel.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FileUploadWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FileUploadWizard.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/RenameFolderWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/RenameFolderWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/RenameFileWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/CustomGridView.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FileMeta.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FileMetaGrid.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FolderMeta.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FolderMetaGrid.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/TagsPanel.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/MainPanel.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/MediamanagerWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/NewFolderWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FileReplaceWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/PropertiesWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FileDetailWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FolderDetailWindow.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/FolderPropertiesPanel.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/UploadStatusBar.js',
            '@PhlexibleMediaManagerBundle/Resources/scripts/UploadChecker.js',

            '@PhlexibleMediaManagerBundle/Resources/scripts/portlet/LatestFiles.js',

            '@PhlexibleMediaManagerBundle/Resources/scripts/menuhandle/MediaHandle.js',
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        return array(
            '@PhlexibleMediaManagerBundle/Resources/styles/mediamanager.css',
            '@PhlexibleMediaManagerBundle/Resources/styles/portlet.css',
            '@PhlexibleMediaManagerBundle/Resources/styles/filefield.css',
        );
    }
}
