<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\MappedContent;

use Phlexible\Bundle\GuiBundle\Asset\Filter\ChainContentFilter;
use Phlexible\Bundle\GuiBundle\Asset\Filter\EnsureTrailingSeparatorContentFilter;
use Phlexible\Bundle\GuiBundle\Asset\Filter\LineSeparatorContentFilter;
use Phlexible\Bundle\GuiBundle\Asset\SourceMap\SourceMapBuilder;
use Puli\Repository\Resource\FileResource;

/**
 * Mapped content builder
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class MappedContentBuilder
{
    /**
     * @param string         $name
     * @param FileResource[] $resources
     * @param callable       $sanitizePath
     * @param callable|null  $prefixContent
     * @param callable|null  $filterContent
     *
     * @return MappedContent
     */
    public function build($name, array $resources, callable $sanitizePath = null, callable $prefixContent = null, callable $filterContent = null)
    {
        $filter = new ChainContentFilter(array(
            new LineSeparatorContentFilter(PHP_EOL),
            new EnsureTrailingSeparatorContentFilter(PHP_EOL),
        ));

        $line = 0;
        $content = '';
        if ($prefixContent) {
            $content .= $filter->filter($prefixContent());
            $line = substr_count($content, PHP_EOL) + 1;
        }
        $sourceMapBuilder = new SourceMapBuilder($name, $line);

        foreach ($resources as $resource) {
            $fileContent = $filter->filter($resource->getBody());
            $content .= $fileContent;

            $path = $resource->getPath();
            if ($sanitizePath) {
                $path = $sanitizePath($path);
            }

            $sourceMapBuilder->add($path, $fileContent);
        }

        $map = $sourceMapBuilder->getSourceMap();

        if ($filterContent) {
            $content = $filterContent($content);
        }

        return new MappedContent($content, $map->toJson());
    }
}
