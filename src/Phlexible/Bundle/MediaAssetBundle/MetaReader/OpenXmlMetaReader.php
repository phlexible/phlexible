<?php
/**
 * phlexible
 *
 * @copyright 2007-2013 brainbits GmbH (http://www.brainbits.net)
 * @license   proprietary
 */

namespace Phlexible\Bundle\MediaAssetBundle\MetaReader;

use Phlexible\Bundle\MediaAssetBundle\MetaBag;
use Phlexible\Bundle\MediaAssetBundle\MetaData;
use Phlexible\Bundle\MediaSiteBundle\File\FileInterface;

/**
 * OpenXML meta reader
 *
 * @author Stephan Wentz <sw@brainbits.net>
 */
class OpenXmlMetaReader implements MetaReaderInterface
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

        $metaData = new MetaData();
        $metaData->setTitle('OpenXML');

        try {
            $mydoc = \OpenXMLDocumentFactory::openDocument($filename);

            $createdBy = (string)$mydoc->getCreator();
            if (!empty($createdBy)) {
                $metaData->set('createdBy', (string)$createdBy);
            }

            $subject = (string)$mydoc->getSubject();
            if (!empty($subject)) {
                $metaData->set('subject', (string)$subject);
            }

            $keywords = (string)$mydoc->getKeywords();
            if (!empty($keywords)) {
                $metaData->set('keywords', (string)$keywords);
            }

            $description = (string)$mydoc->getDescription();
            if (!empty($description)) {
                $metaData->set('description', (string)$description);
            }

            $createdAt = (string)$mydoc->getCreationDate();
            if (!empty($createdAt)) {
                $metaData->set('createdAt', (string)$createdAt);
            }

            $lastModified = (string)$mydoc->getLastModificationDate();
            if (!empty($lastModified)) {
                $metaData->set('lastModified', (string)$lastModified);
            }

            $lastModifiedBy = (string)$mydoc->getLastWriter();
            if (!empty($lastModifiedBy)) {
                $metaData->set('lastModified', (string)$lastModifiedBy);
            }

            $revision = (string)$mydoc->getRevision();
            if (!empty($revision)) {
                $metaData->set('revision', (string)$revision);
            }

            $application = (string)$mydoc->getApplication();
            if (!empty($application)) {
                $metaData->set('application', (string)$application);
            }

            $company = (string)$mydoc->getCompany();
            if (!empty($company)) {
                $metaData->set('company', (string)$company);
            }

            $metaBag->add($metaData);
        } catch (\Exception $e) {
        }
    }
}
