<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Model;

use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Documenttype icon resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconResolver
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
     * Resolve icon
     *
     * @param Documenttype $documenttype
     * @param int          $requestedSize
     *
     * @return string
     */
    public function resolve(Documenttype $documenttype, $requestedSize = null)
    {
        $icons = $documenttype->getIcons();
        if (!isset($icons[$requestedSize])) {
            return null;
        }

        return $this->locator->locate($icons[$requestedSize], null, true);
    }
}
