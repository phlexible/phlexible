<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\Textimage;

use Brainbits\Imagemagick\Convert;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Textimage renderer
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class TextimageRenderer
{
    /**
     * @var array
     */
    private $locationAngles = [
        'west'  => 270,
        'east'  => 90,
        'north' => 0,
        'south' => 0
    ];

    /**
     * @var array
     */
    private $bundles;

    /**
     * @var Convert
     */
    private $convert;

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param array   $bundles
     * @param Convert $convert
     * @param string  $cacheDir
     */
    public function __construct(array $bundles, Convert $convert, $cacheDir)
    {
        $this->bundles = $bundles;
        $this->convert = $convert;
        $this->cacheDir = $cacheDir;
    }

    /**
     * Create an image based on a translation string
     *
     * @param string $text
     * @param string $color
     * @param string $loc
     * @param string $iconPath
     *
     * @return string
     */
    public function get($text, $color, $loc = null, $iconPath = null)
    {
        if (!in_array($loc, array_keys($this->locationAngles))) {
            $loc = 'west';
        }

        $cacheId = md5(
            implode(
                '__',
                [
                    $text .
                    $loc .
                    $color . $iconPath
                ]
            )
        );

        $cacheFilename = $this->cacheDir . '/' . $cacheId . '.png';

        $filesystem = new Filesystem();
        if ($filesystem->exists($cacheFilename)) {
            return $cacheFilename;
        }

        if (!$filesystem->exists(dirname($cacheFilename))) {
            $filesystem->mkdir(dirname($cacheFilename), 0777);
        }

        $class = $this->bundles['PhlexibleGuiBundle'];
        $reflection = new \ReflectionClass($class);
        $font = dirname($reflection->getFileName()) . '/Resources/public/fonts/arial.ttf';

        $options = $this->convert->options()
            ->background('transparent')
            ->fill($color)
            ->font($font)
            ->pointSize(16)
            ->size('x16')
            ->gravity('center')
            ->label($text);

        if ($iconPath) {
            $options
                ->file($iconPath)
                ->lastSwap()
                ->size('5x16')
                ->xc('none')
                ->lastSwap()
                ->appendLeftToRight();
        }

        if ($this->locationAngles[$loc]) {
            $options
                ->rotate($this->locationAngles[$loc]);
        }

        $this->convert
            ->write($cacheFilename, $options->getOptions());

        return $cacheFilename;
    }
}
