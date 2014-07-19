<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaManagerBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Media manager asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaManagerAssetProvider implements AssetProviderInterface
{
    /**
     * @var FileLocator
     */
    private $locator;

    /**
     * @param FileLocator $locator
     */
    public function __construct(FileLocator $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxScriptsCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/Ext.form.FileField.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/Ext.form.DownloadFileField.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/Ext.form.ImageFileField.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/Ext.form.FlashFileField.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/Ext.form.VideoFileField.js')),

            //new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/plupload.js')),
            //new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/moxie.js')),
            //new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/plupload.dev.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/plupload.full.min.js')),
            //new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/SwfUpload.js')),
            //new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/Ext.ux.SwfUploadPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/ux/Ext.ux.LocationBar.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getUxCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/styles/SwfUploadPanel.css')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/styles/Ext.ux.LocationBar.css')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getScriptsCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/model/File.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/util/Bullets.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/Templates.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FolderTreeNodeUI.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FolderTree.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FilesGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/AttributesPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FilePreviewPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FileVersionsPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FileUploadWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FileUploadWizard.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/RenameFolderWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/RenameFolderWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/RenameFileWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/CustomGridView.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FileMeta.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FileMetaGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FolderMeta.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FolderMetaGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/TagsPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/MediamanagerWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/NewFolderWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FileReplaceWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/PropertiesWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FileDetailWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FolderDetailWindow.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/FolderPropertiesPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/UploadStatusBar.js')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/UploadChecker.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/options/MediaSettings.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/portlet/LatestFiles.js')),

            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/scripts/menuhandle/MediaHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/styles/mediamanager.css')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/styles/portlet.css')),
            new FileAsset($this->locator->locate('@PhlexibleMediaManagerBundle/Resources/styles/filefield.css')),
        ));

        return $collection;
    }
}
