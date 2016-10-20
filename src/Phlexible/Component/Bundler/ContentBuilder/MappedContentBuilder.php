<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\ContentBuilder;

use Phlexible\Component\GuiAsset\Content\MappedContent;
use Phlexible\Component\GuiAsset\Filter\ChainContentFilter;
use Phlexible\Component\GuiAsset\Filter\EnsureTrailingSeparatorContentFilter;
use Phlexible\Component\GuiAsset\Filter\LineSeparatorContentFilter;
use Phlexible\Component\GuiAsset\ResourceResolver\ResolvedResources;
use Phlexible\Component\GuiAsset\SourceMap\SourceMapBuilder;

/**
 * Mapped content builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MappedContentBuilder
{
    /**
     * @param string            $name
     * @param ResolvedResources $resources
     * @param callable          $sanitizePath
     * @param callable|null     $prefixContent
     * @param callable|null     $filterContent
     *
     * @return MappedContent
     */
    public function build($name, ResolvedResources $resources, $sanitizePath = null, $prefixContent = null, $filterContent = null)
    {
        $filter = new ChainContentFilter(array(
            new LineSeparatorContentFilter(PHP_EOL),
            new EnsureTrailingSeparatorContentFilter(PHP_EOL),
        ));

        $line = 0;
        $content = '';
        if (is_callable($prefixContent)) {
            $content .= $filter->filter($prefixContent($resources));
            $line = substr_count($content, PHP_EOL) + 1;
        }
        $sourceMapBuilder = new SourceMapBuilder($name, $line);

        foreach ($resources->getResources() as $resource) {
            $fileContent = $filter->filter($resource->getBody());
            $content .= $fileContent;

            $path = $resource->getPath();
            if (is_callable($sanitizePath)) {
                $path = $sanitizePath($path);
            }

            $sourceMapBuilder->add($path, $fileContent);
        }

        $map = $sourceMapBuilder->getSourceMap();

        if (is_callable($filterContent)) {
            $content = $filterContent($content);
        }

        return new MappedContent($content, $map->toJson());
    }
}
