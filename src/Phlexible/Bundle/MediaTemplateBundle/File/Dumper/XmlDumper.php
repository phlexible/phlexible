<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\File\Dumper;

use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;
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