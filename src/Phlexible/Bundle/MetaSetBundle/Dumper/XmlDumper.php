<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MetaSetBundle\Dumper;

use Phlexible\Bundle\MetaSetBundle\MetaSet;

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
    public function dump($file, MetaSet $metaSet)
    {
        if (!file_exists(dirname($file))) {
            if (!mkdir(dirname($file), 0777, true)) {
                throw new \Exception();
            }
        }

        $xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><metaSet/>');
        $xml->addAttribute('id', $metaSet->getId());
        $xml->addAttribute('title', $metaSet->getTitle());
        $xml->addAttribute('dataClass', $metaSet->getDataClass());
        $xml->addAttribute('revision', $metaSet->getRevision());

        foreach ($metaSet->getFields() as $field) {
            $parameterNode = $xml->addChild('item');
            $parameterNode->addAttribute('key', $field->getKey());
            $parameterNode->addAttribute('type', $field->getType());
            $parameterNode->addAttribute('options', $field->getOption());
            $parameterNode->addAttribute('required', $field->isRequired() ? 1 : 0);
            $parameterNode->addAttribute('readonly', $field->isReadonly() ? 1 : 0);
            $parameterNode->addAttribute('synchronized', $field->isSynchronized() ? 1 : 0);
        }

        $xml->asXML($file);
    }
}