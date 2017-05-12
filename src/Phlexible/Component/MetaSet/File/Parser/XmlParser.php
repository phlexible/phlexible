<?php

/*
 * This file is part of the phlexible package.
 *
 * (c) Stephan Wentz <sw@brainbits.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Phlexible\Component\MetaSet\File\Parser;

use Phlexible\Component\MetaSet\Domain\MetaSet;
use Phlexible\Component\MetaSet\Domain\MetaSetField;

/**
 * XML parser.
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class XmlParser implements ParserInterface
{
    /**
     * {@inheritdoc}
     */
    public function parse($content)
    {
        $xml = simplexml_load_string($content);

        $xmlAttributes = $xml->attributes();
        $id = (string) $xmlAttributes['id'];
        $name = (string) $xmlAttributes['name'];
        $revision = (int) $xmlAttributes['revision'];
        $createdBy = (string) $xmlAttributes['createdBy'];
        $createdAt = new \DateTime((string) $xmlAttributes['createdAt']);
        $modifiedBy = (string) $xmlAttributes['modifiedBy'];
        $modifiedAt = new \DateTime((string) $xmlAttributes['modifiedAt']);

        $metaSet = new MetaSet();
        $metaSet
            ->setId($id)
            ->setName($name)
            ->setRevision($revision)
            ->setCreatedAt($createdAt)
            ->setCreatedBy($createdBy)
            ->setModifiedAt($modifiedAt)
            ->setModifiedBy($modifiedBy);

        foreach ($xml->fields->field as $fieldNode) {
            $fieldNodeAttributes = $fieldNode->attributes();
            $id = (string) $fieldNodeAttributes['id'];
            $name = (string) $fieldNodeAttributes['name'];
            $type = (string) $fieldNodeAttributes['type'];
            $options = (string) $fieldNodeAttributes['options'];
            $readonly = (bool) (string) $fieldNodeAttributes['readonly'];
            $required = (bool) (string) $fieldNodeAttributes['required'];
            $synchronized = (bool) (string) $fieldNodeAttributes['synchronized'];
            $field = new MetaSetField();
            $field
                ->setId($id)
                ->setName($name)
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
