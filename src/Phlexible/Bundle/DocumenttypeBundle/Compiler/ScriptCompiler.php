<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Compiler;

use Phlexible\Bundle\DocumenttypeBundle\Model\DocumenttypeCollection;

/**
 * Script compiler
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
    public function compile(DocumenttypeCollection $documenttypes)
    {
        $classMap = [];
        foreach ($documenttypes->getAll() as $documenttype) {
            $classMapItem = [
                'cls' => 'p-documenttype-' . $documenttype->getKey()
            ];

            foreach ($documenttype->getTitles() as $key => $title) {
                $classMapItem[$key] = $title;
            }

            $classMap[$documenttype->getKey()] = $classMapItem;
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
        if ($classname.classMap[documentType]) {
            return $classname.classMap[documentType][Phlexible.Config.get("language.backend", "en")];
        }
        return $classname.classMap["_unknown"][Phlexible.Config.get("language.backend", "en")];
    },
    classMap: $json
};
EOL;

        return $script;
    }
}
