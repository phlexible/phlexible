<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Component\MetaSet\File\Dumper;

use Phlexible\Component\MetaSet\Model\MetaSetInterface;
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
    public function dump($file, MetaSetInterface $metaSet)
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists(dirname($file))) {
            $filesystem->mkdir(dirname($file));
        }

        $xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><metaSet/>');
        $xml->addAttribute('id', $metaSet->getId());
        $xml->addAttribute('name', $metaSet->getName());
        $xml->addAttribute('createdAt', $metaSet->getCreatedAt()->format('Y-m-d H:i:s'));
        $xml->addAttribute('createUser', $metaSet->getCreateUser());
        $xml->addAttribute('modifiedAt', $metaSet->getModifiedAt()->format('Y-m-d H:i:s'));
        $xml->addAttribute('modifyUser', $metaSet->getModifyUser());
        $xml->addAttribute('revision', $metaSet->getRevision());

        $fieldsNode = $xml->addChild('fields');
        foreach ($metaSet->getFields() as $field) {
            $fieldNode = $fieldsNode->addChild('field');
            $fieldNode->addAttribute('id', $field->getId());
            $fieldNode->addAttribute('name', $field->getName());
            $fieldNode->addAttribute('type', $field->getType());
            $fieldNode->addAttribute('options', $field->getOptions());
            $fieldNode->addAttribute('readonly', $field->isReadonly() ? 1 : 0);
            $fieldNode->addAttribute('required', $field->isRequired() ? 1 : 0);
            $fieldNode->addAttribute('synchronized', $field->isSynchronized() ? 1 : 0);
        }

        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $dom->save($file);
    }
}
