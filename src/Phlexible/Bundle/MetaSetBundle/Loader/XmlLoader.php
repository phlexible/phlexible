<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Loader;

use Phlexible\Bundle\MetaSetBundle\MetaSet\MetaSet;
use Phlexible\Bundle\MetaSetBundle\MetaSet\MetaSetField;

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
        $title = (string) $xmlAttributes['title'];
        $dataClass = (string) $xmlAttributes['dataClass'];
        $revision = (int) $xmlAttributes['revision'];

        $metaSet = new MetaSet();
        $metaSet
            ->setId($id)
            ->setTitle($title)
            ->setRevision($revision);

        foreach ($xml->field as $fieldNode) {
            $fieldAttributes = $fieldNode->attributes();
            $key = (string) $fieldAttributes['key'];
            $type = (string) $fieldAttributes['type'];
            $options = (string) $fieldAttributes['options'];
            $readonly = (bool) $fieldAttributes['readonly'];
            $required = (bool) $fieldAttributes['required'];
            $synchronized = (bool) $fieldAttributes['synchronized'];

            $field = new MetaSetField();
            $field
                ->setKey($key)
                ->setType($type)
                ->setOptions($options)
                ->setReadonly($readonly)
                ->setRequired($required)
                ->setSynchronized($synchronized);

            $metaSet->addField($field);
        }

        return $metaSet;
    }
}