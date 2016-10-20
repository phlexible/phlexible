<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MediaTemplate\File\Dumper;

use Phlexible\Component\MediaTemplate\Model\TemplateInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * XML dumper
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlDumper implements DumperInterface
{
    /**
     * {@inheritdoc}
     */
    public function getExtension()
    {
        return 'xml';
    }

    /**
     * {@inheritdoc}
     */
    public function dump($file, TemplateInterface $template)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists(dirname($file))) {
            $filesystem->mkdir(dirname($file));
        }

        $xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><mediaTemplate/>');
        $xml->addAttribute('key', $template->getKey());
        $xml->addAttribute('type', $template->getType());
        $xml->addAttribute('class', get_class($template));
        $xml->addAttribute('cache', $template->getCache() ? 1 : 0);
        $xml->addAttribute('system', $template->getSystem() ? 1 : 0);
        $xml->addAttribute('revision', $template->getRevision());

        foreach ($template->getParameters() as $key => $value) {
            $parameterNode = $xml->addChild('parameter', $value);
            $parameterNode->addAttribute('key', $key);
        }

        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($file);
    }
}
