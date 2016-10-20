<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Bundle\GuiBundle\Asset;

use Phlexible\Component\Bundler\Builder\ResolvingBuilder;
use Phlexible\Component\Bundler\ResourceResolver\ResolvedResources;

/**
 * CSS builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssBuilder extends ResolvingBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function getFilename()
    {
        return 'gui.css';
    }

    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return 'phlexible/styles';
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function sanitizePath($path)
    {
        return preg_match('#^/phlexible/([a-z0-9\-_.]+)/styles/([/A-Za-z0-9\-_.]+\.css)$#', $path, $match)
            ? $match[1] . '/' . $match[2]
            : $path;
    }

    /**
     * @param ResolvedResources $resources
     *
     * @return string
     */
    protected function prefixContent(ResolvedResources $resources)
    {
        $prefix = '/* CSS created on: ' . date('Y-m-d H:i:s') . ' */' . PHP_EOL;

        return $prefix;
    }
}
