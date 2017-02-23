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
 * Scripts builder.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptsBuilder extends ResolvingBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function getFilename()
    {
        return 'gui.js';
    }

    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return 'phlexible/scripts';
    }

    /**
     * {@inheritdoc}
     */
    protected function sanitizePath($path)
    {
        return preg_match('#^/phlexible/([a-z0-9\-_.]+)/scripts/([/A-Za-z0-9\-_.]+\.js)$#', $path, $match)
            ? $match[1].'/'.$match[2]
            : $path;
    }

    /**
     * {@inheritdoc}
     */
    protected function prefixContent(ResolvedResources $resources)
    {
        $prefix = '/* JS created on: '.date('Y-m-d H:i:s').' */'.PHP_EOL;

        if (!$this->isDebug() || !count($resources->getUnusedResources())) {
            return $prefix;
        }

        $prefix .= '/* '.PHP_EOL;
        $prefix .= ' * Unused resources:'.PHP_EOL;
        foreach ($resources->getUnusedResources() as $unusedResource) {
            $prefix .= ' * '.$unusedResource->getPath().PHP_EOL;
        }
        $prefix .= ' */'.PHP_EOL;

        return $prefix;
    }
}
