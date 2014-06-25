<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\DocumenttypeBundle\Compiler\CssCompiler;
use Phlexible\Bundle\DocumenttypeBundle\Compiler\ScriptCompiler;
use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Documenttypes asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class DocumenttypesAssetProvider implements AssetProviderInterface
{
    /**
     * @var DocumenttypeManagerInterface
     */
    private $documenttypeManager;

    /**
     * @var ScriptCompiler
     */
    private $scriptCompiler;

    /**
     * @var CssCompiler
     */
    private $cssCompiler;

    /**
     * @var FileLocator
     */
    private $locator;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param DocumenttypeManagerInterface $documenttypeManager
     * @param ScriptCompiler               $scriptCompiler
     * @param CssCompiler                  $cssCompiler
     * @param FileLocator                  $locator
     * @param string                       $cacheDir
     */
    public function __construct(DocumenttypeManagerInterface $documenttypeManager,
                                ScriptCompiler $scriptCompiler,
                                CssCompiler $cssCompiler,
                                FileLocator $locator,
                                $cacheDir)
    {
        $this->documenttypeManager = $documenttypeManager;
        $this->scriptCompiler = $scriptCompiler;
        $this->cssCompiler = $cssCompiler;
        $this->locator = $locator;
        $this->cacheDir = $cacheDir;
    }

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
        $collection = $this->documenttypeManager->getCollection();
        $cacheFile = $this->cacheDir . '/' . $collection->getHash() . '.js';
        if (!file_exists($cacheFile)) {
            file_put_contents($cacheFile, $this->scriptCompiler->compile($this->documenttypeManager->getCollection()));
        }

        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleDocumenttypeBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleDocumenttypeBundle/Resources/scripts/DocumenttypesGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleDocumenttypeBundle/Resources/scripts/MimetypesGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleDocumenttypeBundle/Resources/scripts/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleDocumenttypeBundle/Resources/scripts/model/Documenttype.js')),

            new FileAsset($this->locator->locate('@PhlexibleDocumenttypeBundle/Resources/scripts/menuhandle/DocumenttypesHandle.js')),

            new FileAsset($cacheFile),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = $this->documenttypeManager->getCollection();
        $cacheFile = $this->cacheDir . '/' . $collection->getHash() . '.css';
        if (!file_exists($cacheFile)) {
            file_put_contents($cacheFile, $this->cssCompiler->compile($this->documenttypeManager->getCollection()));
        }

        $collection = new AssetCollection(array(
            new FileAsset((string) $cacheFile),
        ));

        return $collection;
    }
}
