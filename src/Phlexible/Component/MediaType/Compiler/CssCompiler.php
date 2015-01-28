<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MediaType\Compiler;

use Phlexible\Component\MediaType\Model\MediaTypeCollection;

/**
 * CSS generator
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class CssCompiler implements CompilerInterface
{
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
    public function compile(MediaTypeCollection $mediaTypes)
    {
        $sizes = [16 => 'small']; //, 32 => 'medium', 48 => 'tile');

        $classname = $this->getClassname();

        $styles = array();
        foreach ($mediaTypes->all() as $mediaType) {
            $name = $mediaType->getName();

            foreach ($sizes as $size => $sizeTitle) {
                $styles[] = sprintf('.p-%s-%s-small {background-image:url(//COMPONENTSPATH/phlexiblemediatype/mimetypes16/%s.gif) !important;}', $classname, $name, $name);
            }
        }

        return implode(PHP_EOL, $styles);
    }
}
