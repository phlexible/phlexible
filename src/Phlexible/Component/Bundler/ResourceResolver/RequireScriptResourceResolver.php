<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\GuiAsset\ResourceResolver;

use Doctrine\Common\Collections\ArrayCollection;
use Puli\Repository\Resource\FileResource;

/**
 * Require script resource resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class RequireScriptResourceResolver implements ResourceResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function resolve(array $resources)
    {
        $entryPoints = array();

        foreach ($resources as $resource) {
            if (preg_match('#^/phlexible/[a-z0-9\-_.]+/scripts/[A-Za-z0-9\-_.]+\.js$#', $resource->getPath())) {
                $entryPoints[$resource->getPath()] = $resource;
            }
        }

        $files = array();
        foreach ($resources as $resource) {
            /* @var $resource FileResource */

            $body = $resource->getBody();

            $file = new \stdClass();
            $file->resource = $resource;
            $file->path = $resource->getPath();
            $file->file = $resource->getFilesystemPath();
            $file->requires = array();
            $file->provides = array();

            preg_match_all('/Ext\.provide\(["\'](.+)["\']\)/', $body, $matches);
            foreach ($matches[1] as $provide) {
                $file->provides[] = $provide;
            }

            preg_match_all('/Ext\.require\(["\'](.+)["\']\)/', $body, $matches);
            foreach ($matches[1] as $require) {
                $file->requires[] = $require;
            }

            $files[$resource->getPath()] = $file;
        }

        $prototypes = $files['/phlexible/phlexiblegui/scripts/prototypes.js'];
        $functions = $files['/phlexible/phlexiblegui/scripts/functions.js'];
        $global = $files['/phlexible/phlexiblegui/scripts/global.js'];
        unset($files['/phlexible/phlexiblegui/scripts/prototypes.js']);
        unset($files['/phlexible/phlexiblegui/scripts/functions.js']);
        unset($files['/phlexible/phlexiblegui/scripts/global.js']);

        $files = array_merge(array(
            '/phlexible/phlexiblegui/scripts/prototypes.js' => $prototypes,
            '/phlexible/phlexiblegui/scripts/functions.js' => $functions,
            '/phlexible/phlexiblegui/scripts/global.js' => $global,
        ), $files);

        $entryPointFiles = array_intersect_key($files, $entryPoints);

        $symbols = array();
        foreach ($files as $file) {
            foreach ($file->provides as $provide) {
                $symbols[$provide] = $file;
            }
        }

        $results = new ArrayCollection();

        function addToResult($file, ArrayCollection $results, array $symbols) {
            if (!empty($file->added)) {
                return;
            }

            $file->added = true;

            if (!empty($file->requires)) {
                foreach ($file->requires as $require) {
                    if (!isset($symbols[$require])) {
                        throw new \Exception("Symbol '$require' not found for file {$file->file}.");
                    }
                    addToResult($symbols[$require], $results, $symbols);
                }
            }

            $results->set($file->path, $file->resource);
        };

        foreach ($entryPointFiles as $file) {
            addToResult($file, $results, $symbols);
        }

        $unusedResources = array();
        foreach ($files as $path => $file) {
            if (empty($file->added)) {
                $unusedResources[$path] = $file->resource;
            }
        }
        ksort($unusedResources);

        return new ResolvedResources($results->getValues(), $unusedResources);
    }
}
