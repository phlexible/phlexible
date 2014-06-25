<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\ContentchannelBundle\File\Loader;

use Phlexible\Bundle\ContentchannelBundle\Entity\Contentchannel;

/**
 * XML loader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlLoader implements LoaderInterface
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
    public function load($file)
    {
        $xml = simplexml_load_file($file);

        $xmlAttributes = $xml->attributes();
        $id = (string) $xmlAttributes['id'];
        $uniqueId = (string) $xmlAttributes['uniqueId'];

        $template = new Contentchannel();
        $template
            ->setId($id)
            ->setUniqueId($uniqueId)
            ->setTitle((string) $xml->title)
            ->setIcon((string) $xml->icon)
            ->setTemplateFolder((string) $xml->templateFolder)
            ->setRendererClassname((string) $xml->rendererClassname)
        ;

        return $template;
    }
}