<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaType\Loader;

use Phlexible\Component\MediaType\Model\MediaType;

/**
 * XML media type loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlLoader implements LoaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function supports($file)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function load($filename)
    {
        $xml = simplexml_load_file($filename);

        $mediaType = new MediaType();

        $attrs = $xml->attributes();
        $mediaType
            ->setName((string) $attrs['key'])
            ->setCategory((string) $xml->type);

        if ($xml->titles->count()) {
            if ($xml->titles->title->count()) {
                foreach ($xml->titles->title as $titleNode) {
                    $titleNodeAttrs = $titleNode->attributes();
                    $lang = (string) $titleNodeAttrs['lang'];
                    $title = (string) $titleNode;
                    if ($lang && $title) {
                        $mediaType->setTitle($lang, $title);
                    }
                }
            }
        }

        if ($xml->mimetypes->count()) {
            if ($xml->mimetypes->mimetype->count()) {
                foreach ($xml->mimetypes->mimetype as $mimetypeNode) {
                    $mimetype = (string) $mimetypeNode;
                    if ($mimetype) {
                        $mediaType->addMimetype($mimetype);
                    }
                }
            }
        }

        if ($xml->icons->count()) {
            if ($xml->icons->icon->count()) {
                foreach ($xml->icons->icon as $iconNode) {
                    $iconAttributes = $iconNode->attributes();
                    $size = (int) $iconAttributes['size'];
                    $icon = (string) $iconNode;
                    if ($icon) {
                        $mediaType->setIcon($size, $icon);
                    }
                }
            }
        }

        return $mediaType;
    }

}
