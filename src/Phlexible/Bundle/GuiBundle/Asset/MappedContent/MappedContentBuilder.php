<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\GuiBundle\Asset\MappedContent;

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
     * @param callable       $sanitize
     * @param callable|null  $prefix
     * @param callable|null  $filter
     *
     * @return MappedContent
     */
    public function build($name, array $resources, callable $sanitize, callable $prefix = null, callable $filter = null)
    {
        $content = '';
        $content .= '/* Created: ' . date('Y-m-d H:i:s') . ' */' . PHP_EOL;
        if ($prefix) {
            $content .= $prefix();
        }
        $line = substr_count($content, PHP_EOL);
        $sourceMapBuilder = new SourceMapBuilder($name, $line);
        foreach ($resources as $path => $resource) {
            $fileContent = $resource->getBody() . PHP_EOL;
            $content .= $fileContent;

            $sourceMapBuilder->add($sanitize($resource), $fileContent);
        }

        $map = $sourceMapBuilder->getSourceMap();

        if ($filter) {
            $content = $filter($content);
        }

        return new MappedContent($content, $map->toJson());
    }
}
