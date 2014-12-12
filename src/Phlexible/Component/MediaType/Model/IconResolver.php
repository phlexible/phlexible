<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\Model;

use Symfony\Component\HttpKernel\Config\FileLocator;

/**
 * Media type icon resolver
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
     * @param MediaType $mediaType
     * @param int       $requestedSize
     *
     * @return string
     */
    public function resolve(MediaType $mediaType, $requestedSize = null)
    {
        $icons = $mediaType->getIcons();
        if (!count($icons)) {
            return null;
        }
        ksort($icons);

        if (isset($icons[$requestedSize])) {
            $icon = $icons[$requestedSize];
        } else {
            $icon = null;
            foreach ($icons as $size => $dummyIcon) {
                if ($size > $requestedSize) {
                    $icon = $dummyIcon;
                    break;
                }
            }
            if (!$icon) {
                $icon = end($icons);
                //return null;
            }
        }

        return $this->locator->locate($icon, null, true);
    }
}
