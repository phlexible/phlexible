<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaTemplateBundle\File\Loader;

use Phlexible\Bundle\MediaTemplateBundle\Exception\InvalidClassException;
use Phlexible\Bundle\MediaTemplateBundle\Model\TemplateInterface;

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
        $key = (string) $xmlAttributes['key'];
        $type = (string) $xmlAttributes['type'];
        $class = (string) $xmlAttributes['class'];
        $cache = (bool) $xmlAttributes['cache'];
        $system = (bool) $xmlAttributes['system'];
        $revision = (int) $xmlAttributes['revision'];

        if (!class_exists($class)) {
            throw new InvalidClassException("Invalid template class $class");
        }

        /* @var $template TemplateInterface */
        $template = new $class();
        $template
            ->setKey($key)
            ->setType($type)
            ->setCache($cache)
            ->setSystem($system)
            ->setRevision($revision)
            ->setCreatedAt(\DateTime::createFromFormat('U', filectime($file)))
            ->setModifiedAt(\DateTime::createFromFormat('U', filemtime($file)));

        foreach ($xml as $parameterNode) {
            $parameterNodeAttributes = $parameterNode->attributes();
            $key = (string) $parameterNodeAttributes['key'];
            $value = (string) $parameterNode;
            $template->setParameter($key, $value);
        }

        return $template;
    }
}