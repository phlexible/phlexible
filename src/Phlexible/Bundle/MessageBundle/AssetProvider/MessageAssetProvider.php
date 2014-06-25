<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MessageBundle\AssetProvider;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Phlexible\Bundle\GuiBundle\AssetProvider\AssetProviderInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Message asset provider
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MessageAssetProvider implements AssetProviderInterface
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
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/Definitions.js')),

            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/model/Criterium.js')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/model/Filter.js')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/model/Message.js')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/model/Subscription.js')),

            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/view/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/view/FilterPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/view/MessagesGrid.js')),

            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/filter/MainPanel.js')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/filter/ListGrid.js')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/filter/CriteriaForm.js')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/filter/PreviewPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/subscription/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/MainPanel.js')),

            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/portlet/Messages.js')),

            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/scripts/menuhandle/MessagesHandle.js')),
        ));

        return $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getCssCollection()
    {
        $collection = new AssetCollection(array(
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/styles/messages.css')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/styles/filter.css')),
            new FileAsset($this->locator->locate('@PhlexibleMessageBundle/Resources/styles/portlet.css')),
        ));

        return $collection;
    }
}
