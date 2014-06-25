<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\AttributeReader;

use Phlexible\Bundle\MediaAssetBundle\AttributeMetaData;
use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * OpenXML attribute reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OpenXmlAttributeReader implements AttributeReaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        return extension_loaded('zip') && class_exists('OpenXMLDocumentFactory');
    }

    /**
     * {@inheritdoc}
     */
    public function supports(FileInterface $file)
    {
        return in_array(strtolower($file->getAttribute('documenttype')), array('docx', 'xlsx', 'pptx'));
    }

    /**
     * {@inheritdoc}
     */
    public function read(FileInterface $file, MetaBag $metaBag)
    {
        $filename = $file->getPhysicalPath();

        $metaData = new AttributeMetaData();
        $metaData->setTitle('OpenXML attributes');

        try {
            $mydoc = \OpenXMLDocumentFactory::openDocument($filename);
            $data = $mydoc->getExtendedArray();

            foreach($data as $key => $value) {
                if (!empty($value)) {
                    $metaData->set($key, $value);
                }
            }

            $metaBag->add($metaData);
        } catch(\Exception $e) {
        }
    }
}
