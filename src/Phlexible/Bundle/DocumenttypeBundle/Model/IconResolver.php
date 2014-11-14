<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\DocumenttypeBundle\Model;

/**
 * Documenttype icon resolver
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class IconResolver
{
    /**
     * Resolve icon
     *
     * @param Documenttype $documenttype
     * @param int          $neededSize
     *
     * @return string
     */
    public function resolve(Documenttype $documenttype, $neededSize = 16)
    {
        $documentTypeKey = $documenttype->getKey();

        $sizes = array(-1, 16, 32, 48, 256);
        $imgDir = __DIR__ . '/../Resources/public/mimetypes';

        if ($documentTypeKey !== null) {
            $imgFile = $documentTypeKey . '.gif';
        } else {
            $imgFile = '_fallback.gif';
        }

        $i = count($sizes) - 1;
        $size = $sizes[$i];

        if (!is_null($neededSize)) {
            while (!empty($sizes[$i - 1]) &&
                ($sizes[$i] > $neededSize || !file_exists($imgDir . $sizes[$i] . '/' . $imgFile))) {
                $size = $sizes[--$i];
            }
        }

        if ($size == -1) {
            $i = count($sizes) - 1;
            $size = $sizes[$i];

            while (!empty($sizes[$i - 1]) &&
                ($sizes[$i] > $neededSize || !file_exists($imgDir . $sizes[$i] . '/_fallback.gif'))) {
                $size = $sizes[--$i];
            }

            return $imgDir . $size . '/_fallback.gif';
        }

        return $imgDir . $size . '/' . $imgFile;
    }
}
