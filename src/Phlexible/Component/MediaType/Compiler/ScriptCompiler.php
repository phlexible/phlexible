<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\Compiler;

use Phlexible\Component\MediaType\Model\MediaTypeCollection;

/**
 * Script compiler.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class ScriptCompiler implements CompilerInterface
{
    /**
     * @return string
     */
    public function getClassname()
    {
        return 'Phlexible.documenttypes.DocumentTypes';
    }

    /**
     * {@inheritdoc}
     */
    public function compile(MediaTypeCollection $mediaTypes)
    {
        $classMap = [];
        foreach ($mediaTypes->all() as $mediaType) {
            $classMapItem = [
                'cls' => sprintf('p-documenttype-%s', $mediaType->getName()),
            ];

            foreach ($mediaType->getTitles() as $key => $title) {
                $classMapItem[$key] = $title;
            }

            $classMap[$mediaType->getName()] = $classMapItem;
        }

        $classname = $this->getClassname();
        $json = json_encode($classMap, JSON_PRETTY_PRINT);

        $script = <<<EOL
Ext.ns("$classname");
$classname = {
    getClass: function(documentType) {
        if ($classname.classMap[documentType]) {
            return $classname.classMap[documentType].cls;
        }
        return $classname.classMap["_unknown"].cls;
    },
    getText: function(documentType) {
        var language = Phlexible.Config.get("language.backend", "en");
        if ($classname.classMap[documentType]) {
            return $classname.classMap[documentType][language];
        }
        return $classname.classMap["_unknown"][language];
    },
    classMap: $json
};
EOL;

        return $script;
    }
}
