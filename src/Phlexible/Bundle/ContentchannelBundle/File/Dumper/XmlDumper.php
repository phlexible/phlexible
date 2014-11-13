<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\File\Dumper;

use Phlexible\Bundle\ContentchannelBundle\Entity\Contentchannel;
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
    public function dump($file, Contentchannel $contentchannel)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists(dirname($file))) {
            $filesystem->mkdir(dirname($file));
        }

        $xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><mediaTemplate/>');
        $xml->addAttribute('id', $contentchannel->getId());
        $xml->addAttribute('uniqueId', $contentchannel->getUniqueId());
        $xml->addChild('title', $contentchannel->getTitle());
        $xml->addChild('icon', $contentchannel->getIcon());
        $xml->addChild('templateFolder', $contentchannel->getTemplateFolder());
        $xml->addChild('rendererClassname', $contentchannel->getRendererClassname());

        $xml->asXML($file);
    }
}
