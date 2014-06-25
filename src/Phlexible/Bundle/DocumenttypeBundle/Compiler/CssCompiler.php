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
 * CSS generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssCompiler implements CompilerInterface
{
    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getClassname()
    {
        return 'documenttype';
    }

    /**
     * {@inheritdoc}
     */
    public function compile(DocumenttypeCollection $documenttypes)
    {
        $sizes = array(16 => 'small'); //, 32 => 'medium', 48 => 'tile');

        $classname = $this->getClassname();

        $style = '';
        foreach ($documenttypes->getAll() as $documenttype) {
            $key = $documenttype->getKey();

            foreach ($sizes as $size => $sizeTitle) {
                $style .= '.p-' . $classname . '-' . $key . '-small {background-image:url(/BASEPATH/bundles/documenttypes/mimetypes16/' . $key . '.gif) !important;}' . PHP_EOL;
//                $style .= '.m-smallThumbnails .m-'.$cMimeType.' {background-image:url(/BASEPATH/resources/resources/mimetypes16/documenttypes/'.$desc['img'].'.gif) !important;}' . PHP_EOL;
//                $style .= '.m-mediumThumbnails .m-'.$cMimeType.' {background-image:url(/BASEPATH/resources/resources/mimetypes32/documenttypes/'.$desc['img'].'.gif) !important;}' . PHP_EOL;
//                $style .= '.m-tileThumbnails .m-'.$cMimeType.' {background-image:url(/BASEPATH/resources/resources/mimetypes48/documenttypes/'.$desc['img'].'.gif) !important;}' . PHP_EOL;
            }
        }

        return $style;
    }
}
