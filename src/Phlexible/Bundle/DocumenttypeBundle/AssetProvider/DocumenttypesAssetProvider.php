<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\AssetProvider;

use Phlexible\Bundle\DocumenttypeBundle\Compiler\CssCompiler;
use Phlexible\Bundle\DocumenttypeBundle\Compiler\ScriptCompiler;
use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeManagerInterface;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

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
     * @var string
     */
    private $cacheDir;

    /**
     * @param DocumenttypeManagerInterface $documenttypeManager
     * @param ScriptCompiler               $scriptCompiler
     * @param CssCompiler                  $cssCompiler
     * @param string                       $cacheDir
     */
    public function __construct(DocumenttypeManagerInterface $documenttypeManager,
                                ScriptCompiler $scriptCompiler,
                                CssCompiler $cssCompiler,
                                $cacheDir)
    {
        $this->documenttypeManager = $documenttypeManager;
        $this->scriptCompiler = $scriptCompiler;
        $this->cssCompiler = $cssCompiler;
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

        return array(
            '@PhlexibleDocumenttypeBundle/Resources/scripts/Definitions.js',

            '@PhlexibleDocumenttypeBundle/Resources/scripts/DocumenttypesGrid.js',
            '@PhlexibleDocumenttypeBundle/Resources/scripts/MimetypesGrid.js',
            '@PhlexibleDocumenttypeBundle/Resources/scripts/MainPanel.js',

            '@PhlexibleDocumenttypeBundle/Resources/scripts/model/Documenttype.js',

            '@PhlexibleDocumenttypeBundle/Resources/scripts/menuhandle/DocumenttypesHandle.js',

            $cacheFile,
        );
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

        return array(
            (string) $cacheFile,
        );
    }
}
