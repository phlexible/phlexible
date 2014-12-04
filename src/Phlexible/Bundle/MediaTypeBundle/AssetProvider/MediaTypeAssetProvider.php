<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTypeBundle\AssetProvider;

use Phlexible\Component\MediaType\Compiler\CssCompiler;
use Phlexible\Component\MediaType\Compiler\ScriptCompiler;
use Phlexible\Component\MediaType\Model\MediaTypeManagerInterface;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;

/**
 * MediaType asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MediaTypeAssetProvider implements AssetProviderInterface
{
    /**
     * @var MediaTypeManagerInterface
     */
    private $mediaTypeManager;

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
     * @param MediaTypeManagerInterface $mediaTypeManager
     * @param ScriptCompiler            $scriptCompiler
     * @param CssCompiler               $cssCompiler
     * @param string                    $cacheDir
     */
    public function __construct(MediaTypeManagerInterface $mediaTypeManager,
                                ScriptCompiler $scriptCompiler,
                                CssCompiler $cssCompiler,
                                $cacheDir)
    {
        $this->mediaTypeManager = $mediaTypeManager;
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
        $collection = $this->mediaTypeManager->getCollection();
        $cacheFile = $this->cacheDir . '/' . $collection->getHash() . '.js';
        if (!file_exists($cacheFile)) {
            file_put_contents($cacheFile, $this->scriptCompiler->compile($this->mediaTypeManager->getCollection()));
        }

        return [
            '@PhlexibleMediaTypeBundle/Resources/scripts/Definitions.js',

            '@PhlexibleMediaTypeBundle/Resources/scripts/DocumenttypesGrid.js',
            '@PhlexibleMediaTypeBundle/Resources/scripts/MimetypesGrid.js',
            '@PhlexibleMediaTypeBundle/Resources/scripts/MainPanel.js',

            '@PhlexibleMediaTypeBundle/Resources/scripts/model/Documenttype.js',

            '@PhlexibleMediaTypeBundle/Resources/scripts/menuhandle/DocumenttypesHandle.js',

            $cacheFile,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = $this->mediaTypeManager->getCollection();
        $cacheFile = $this->cacheDir . '/' . $collection->getHash() . '.css';
        if (!file_exists($cacheFile)) {
            file_put_contents($cacheFile, $this->cssCompiler->compile($this->mediaTypeManager->getCollection()));
        }

        return [
            (string) $cacheFile,
        ];
    }
}
